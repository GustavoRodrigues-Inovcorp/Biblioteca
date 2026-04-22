<?php

namespace App\Livewire\Chat;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\ChatMessageReaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class ChatPage extends Component
{
    use WithFileUploads;

    public string $conversationSearch = '';

    public string $messageBody = '';

    public string $messageSearch = '';

    public bool $isMessageSearchMode = false;

    /** @var array<int, string> */
    public array $messageSearchHistory = [];

    public $messageAttachment = null;

    public ?int $replyingToMessageId = null;

    public ?int $selectedConversationId = null;

    public bool $showRoomForm = false;

    public string $roomName = '';

    public string $roomAvatar = '';

    /** @var array<int> */
    public array $roomParticipantIds = [];

    public string $userSearch = '';

    /** @var array<int> */
    public array $roomInviteIds = [];

    protected $queryString = [
        'selectedConversationId' => ['as' => 'conversation', 'except' => null],
        'conversationSearch' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->selectedConversationId = $this->resolveInitialConversationId();
        $this->messageSearchHistory = session()->get($this->messageSearchHistorySessionKey(), []);
    }

    public function updatedMessageSearch(string $value): void
    {
        $search = trim($value);

        if ($search === '') {
            return;
        }

        $this->recordMessageSearchHistory($search);
    }

    public function updatedConversationSearch(): void
    {
        $this->selectedConversationId = $this->resolveInitialConversationId();
    }

    public function updatedMessageBody(string $value): void
    {
        $conversation = $this->currentConversation();
        $user = auth()->user();

        if (! $conversation || ! $user instanceof User) {
            return;
        }

        $this->syncTypingIndicator($conversation->id, $user->id, trim($value));
    }

    public function selectConversation(int $conversationId): void
    {
        $this->clearTypingIndicator();
        $this->ensureParticipant($conversationId);
        $this->selectedConversationId = $conversationId;
        $this->messageBody = '';
        $this->messageSearch = '';
        $this->isMessageSearchMode = false;
        $this->messageAttachment = null;
        $this->replyingToMessageId = null;
        $this->roomInviteIds = [];
        $this->resetValidation();
    }

    public function toggleMessageSearch(): void
    {
        $this->isMessageSearchMode = ! $this->isMessageSearchMode;

        if (! $this->isMessageSearchMode) {
            $this->messageSearch = '';
            return;
        }

        $this->messageAttachment = null;
        $this->resetValidation('messageAttachment');
    }

    public function clearMessageSearchHistory(): void
    {
        $this->messageSearchHistory = [];
        session()->forget($this->messageSearchHistorySessionKey());
    }

    public function searchFromHistory(string $term): void
    {
        $this->isMessageSearchMode = true;
        $this->messageSearch = trim($term);
    }

    public function toggleRoomForm(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $this->showRoomForm = ! $this->showRoomForm;
    }

    public function createRoom(): void
    {
        $user = auth()->user();
        abort_unless($user?->isAdmin(), 403);

        $validated = $this->validate([
            'roomName' => ['required', 'string', 'max:255'],
            'roomAvatar' => ['nullable', 'string', 'max:2048'],
            'roomParticipantIds' => ['array'],
            'roomParticipantIds.*' => ['integer', Rule::exists('users', 'id')],
        ]);

        $participantIds = array_values(array_unique(array_filter(array_map('intval', $validated['roomParticipantIds'] ?? []), fn (int $id) => $id !== $user->id)));

        $conversation = ChatConversation::query()->create([
            'type' => ChatConversation::TYPE_ROOM,
            'name' => trim($validated['roomName']),
            'avatar' => trim($validated['roomAvatar'] ?? '') ?: null,
            'created_by_id' => $user->id,
        ]);

        $conversation->participants()->sync(array_values(array_unique([
            $user->id,
            ...$participantIds,
        ])));

        $this->reset(['roomName', 'roomAvatar', 'roomParticipantIds', 'showRoomForm']);
        $this->selectedConversationId = $conversation->id;
    }

    public function startDirectConversation(int $userId): void
    {
        $currentUser = auth()->user();

        abort_unless($currentUser instanceof User, 403);
        abort_if($userId === $currentUser->id, 422, 'Não é possível iniciar uma conversa consigo próprio.');

        $otherUser = User::query()->findOrFail($userId);

        $conversation = ChatConversation::query()
            ->where('type', ChatConversation::TYPE_DIRECT)
            ->whereHas('participants', fn ($query) => $query->where('users.id', $currentUser->id))
            ->whereHas('participants', fn ($query) => $query->where('users.id', $otherUser->id))
            ->withCount('participants')
            ->get()
            ->first(fn (ChatConversation $candidate) => $candidate->participants_count === 2);

        if (! $conversation) {
            $conversation = ChatConversation::query()->create([
                'type' => ChatConversation::TYPE_DIRECT,
                'name' => $otherUser->name,
                'avatar' => null,
                'created_by_id' => $currentUser->id,
            ]);

            $conversation->participants()->sync([$currentUser->id, $otherUser->id]);
        }

        $this->selectConversation($conversation->id);
    }

    public function sendMessage(): void
    {
        if ($this->isMessageSearchMode) {
            return;
        }

        $user = auth()->user();
        abort_unless($user instanceof User, 403);

        $conversation = $this->currentConversation();

        if (! $conversation) {
            return;
        }

        $validated = $this->validate([
            'messageBody' => ['nullable', 'string', 'max:4000', 'required_without:messageAttachment'],
            'messageAttachment' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp,pdf,txt,csv,doc,docx,xls,xlsx,ppt,pptx,zip,rar'],
        ]);

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentMime = null;
        $attachmentSize = null;

        if ($this->messageAttachment) {
            $attachmentPath = $this->messageAttachment->store('chat-attachments', 'public');
            $attachmentName = $this->messageAttachment->getClientOriginalName();
            $attachmentMime = $this->messageAttachment->getClientMimeType();
            $attachmentSize = $this->messageAttachment->getSize();
        }

        $conversation->messages()->create([
            'user_id' => $user->id,
            'replied_to_message_id' => $this->replyingToMessageId,
            'body' => trim((string) ($validated['messageBody'] ?? '')),
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_mime' => $attachmentMime,
            'attachment_size' => $attachmentSize,
        ]);

        $conversation->forceFill([
            'last_message_at' => now(),
        ])->save();

        $this->messageBody = '';
        $this->messageAttachment = null;
        $this->replyingToMessageId = null;
        $this->selectedConversationId = $conversation->id;
        $this->clearTypingIndicator();
    }

    public function removeAttachment(): void
    {
        $this->messageAttachment = null;
        $this->resetValidation('messageAttachment');
    }

    public function toggleReaction(int $messageId, string $emoji): void
    {
        $user = auth()->user();
        abort_unless($user instanceof User, 403);

        $allowedEmojis = ['👍', '❤️', '😂', '😮', '😢', '🙏'];
        abort_unless(in_array($emoji, $allowedEmojis, true), 422);

        $conversation = $this->currentConversation();

        if (! $conversation) {
            return;
        }

        $message = ChatMessage::query()
            ->whereKey($messageId)
            ->where('conversation_id', $conversation->id)
            ->first();

        if (! $message) {
            return;
        }

        $existingReaction = ChatMessageReaction::query()
            ->where('chat_message_id', $message->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingReaction && $existingReaction->emoji === $emoji) {
            $existingReaction->delete();

            return;
        }

        ChatMessageReaction::query()->updateOrCreate(
            [
                'chat_message_id' => $message->id,
                'user_id' => $user->id,
            ],
            ['emoji' => $emoji]
        );
    }

    public function replyToMessage(int $messageId): void
    {
        $user = auth()->user();
        abort_unless($user instanceof User, 403);

        $conversation = $this->currentConversation();

        if (! $conversation) {
            return;
        }

        $message = ChatMessage::query()
            ->with('user:id,name')
            ->whereKey($messageId)
            ->where('conversation_id', $conversation->id)
            ->first();

        if (! $message) {
            return;
        }

        $authorName = $message->user?->name ?? 'Utilizador';
        $messagePreview = trim((string) $message->body);

        if ($messagePreview === '' && $message->attachment_name) {
            $messagePreview = '[Anexo: ' . $message->attachment_name . ']';
        }

        $messagePreview = (string) Str::of($messagePreview)->squish()->limit(80, '...');

        if ($messagePreview === '') {
            $messagePreview = '[Mensagem]';
        }

        $quotedText = '> ' . $authorName . ': ' . $messagePreview;

        $this->isMessageSearchMode = false;
        $this->replyingToMessageId = $message->id;
    }

    public function inviteSelectedUsers(): void
    {
        $user = auth()->user();
        abort_unless($user instanceof User, 403);

        $conversation = $this->currentConversation();

        if (! $conversation || ! $conversation->isRoom()) {
            return;
        }

        abort_unless($user->isAdmin() || $conversation->created_by_id === $user->id, 403);

        $validated = $this->validate([
            'roomInviteIds' => ['array'],
            'roomInviteIds.*' => ['integer', Rule::exists('users', 'id')],
        ]);

        $existingIds = $conversation->participants()->pluck('users.id')->all();
        $newIds = array_values(array_diff(array_unique(array_map('intval', $validated['roomInviteIds'] ?? [])), $existingIds));

        if ($newIds !== []) {
            $conversation->participants()->attach($newIds);
        }

        $this->roomInviteIds = [];
    }

    public function removeRoomParticipant(int $userId): void
    {
        $currentUser = auth()->user();
        abort_unless($currentUser instanceof User, 403);

        $conversation = $this->currentConversation();

        if (! $conversation || ! $conversation->isRoom()) {
            return;
        }

        abort_unless($currentUser->isAdmin() || $conversation->created_by_id === $currentUser->id, 403);

        if ($userId === $conversation->created_by_id) {
            return;
        }

        $conversation->participants()->detach($userId);
    }

    public function getConversationsProperty(): EloquentCollection
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return new EloquentCollection();
        }

        return ChatConversation::query()
            ->whereHas('participants', fn ($query) => $query->where('users.id', $user->id))
            ->with([
                'participants:id,name,estado,profile_photo_path',
                'latestMessage.user:id,name',
            ])
            ->withCount('participants')
            ->when(trim($this->conversationSearch) !== '', function ($query): void {
                $search = trim($this->conversationSearch);

                $query->where(function ($subQuery) use ($search): void {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhereHas('participants', fn ($participantQuery) => $participantQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->get();
    }

    public function getSelectedConversationProperty(): ?ChatConversation
    {
        $conversation = $this->currentConversation();
        $messageSearch = trim($this->messageSearch);

        if ($conversation) {
            $conversation->load([
                'participants:id,name,email,estado,profile_photo_path',
                'messages' => function ($query) use ($messageSearch): void {
                    $query->with([
                        'user:id,name,profile_photo_path',
                        'repliedToMessage.user:id,name,profile_photo_path',
                        'reactions.user:id,name,profile_photo_path',
                    ]);

                    if ($messageSearch !== '') {
                        $query->where('body', 'like', "%{$messageSearch}%");
                    }
                },
            ]);
        }

        return $conversation;
    }

    public function getAvailableUsersProperty(): EloquentCollection
    {
        $currentUser = auth()->user();

        if (! $currentUser instanceof User) {
            return new EloquentCollection();
        }

        $query = User::query()
            ->whereKeyNot($currentUser->id)
            ->orderBy('name');

        if (trim($this->userSearch) !== '') {
            $search = trim($this->userSearch);
            $query->where(function ($subQuery) use ($search): void {
                $subQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('estado', 'like', "%{$search}%");
            });
        }

        return $query->get(['id', 'name', 'email', 'estado', 'profile_photo_path', 'role']);
    }

    public function getRoomInviteCandidatesProperty(): EloquentCollection
    {
        $conversation = $this->currentConversation();

        if (! $conversation || ! $conversation->isRoom()) {
            return new EloquentCollection();
        }

        $participantIds = $conversation->participants()->pluck('users.id')->all();

        return User::query()
            ->whereNotIn('id', $participantIds)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'estado', 'profile_photo_path', 'role']);
    }

    public function getTypingParticipantsProperty(): EloquentCollection
    {
        $conversation = $this->selectedConversation;
        $user = auth()->user();

        if (! $conversation || ! $user instanceof User) {
            return new EloquentCollection();
        }

        return $conversation->participants
            ->filter(fn (User $participant) => $participant->id !== $user->id)
            ->filter(fn (User $participant) => Cache::has($this->typingCacheKey($conversation->id, $participant->id)))
            ->values();
    }

    public function render()
    {
        return view('livewire.chat.chat-page', [
            'conversations' => $this->conversations,
            'selectedConversation' => $this->selectedConversation,
            'availableUsers' => $this->availableUsers,
            'roomInviteCandidates' => $this->roomInviteCandidates,
            'typingParticipants' => $this->typingParticipants,
            'reactionEmojis' => ['👍', '❤️', '😂', '😮', '😢', '🙏'],
        ]);
    }

    protected function resolveInitialConversationId(): ?int
    {
        return $this->conversations->first()?->id;
    }

    protected function currentConversation(): ?ChatConversation
    {
        $user = auth()->user();

        if (! $user instanceof User || ! $this->selectedConversationId) {
            return null;
        }

        return ChatConversation::query()
            ->whereKey($this->selectedConversationId)
            ->whereHas('participants', fn ($query) => $query->where('users.id', $user->id))
            ->first();
    }

    protected function ensureParticipant(int $conversationId): void
    {
        $user = auth()->user();

        abort_unless($user instanceof User, 403);

        abort_unless(
            ChatConversation::query()
                ->whereKey($conversationId)
                ->whereHas('participants', fn ($query) => $query->where('users.id', $user->id))
                ->exists(),
            403
        );
    }

    protected function recordMessageSearchHistory(string $search): void
    {
        $history = array_values(array_filter($this->messageSearchHistory, fn (string $item) => $item !== $search));
        array_unshift($history, $search);
        $this->messageSearchHistory = array_slice($history, 0, 10);
        session()->put($this->messageSearchHistorySessionKey(), $this->messageSearchHistory);
    }

    protected function messageSearchHistorySessionKey(): string
    {
        return 'chat.message_search_history';
    }

    protected function syncTypingIndicator(int $conversationId, int $userId, string $messageBody): void
    {
        $cacheKey = $this->typingCacheKey($conversationId, $userId);

        if ($messageBody === '') {
            Cache::forget($cacheKey);

            return;
        }

        Cache::put($cacheKey, true, now()->addSeconds(5));
    }

    protected function clearTypingIndicator(): void
    {
        $conversation = $this->currentConversation();
        $user = auth()->user();

        if (! $conversation || ! $user instanceof User) {
            return;
        }

        Cache::forget($this->typingCacheKey($conversation->id, $user->id));
    }

    protected function typingCacheKey(int $conversationId, int $userId): string
    {
        return 'chat.typing.' . $conversationId . '.' . $userId;
    }
}