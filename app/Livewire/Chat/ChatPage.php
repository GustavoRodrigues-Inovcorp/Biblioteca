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

    public bool $isPingOpen = false;

    /** @var array<int> */
    public array $selectedPingUserIds = [];

    public string $directSearch = '';

    public string $notificationMode = 'all';

    protected $queryString = [
        'selectedConversationId' => ['as' => 'conversation', 'except' => null],
        'conversationSearch' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->selectedConversationId = $this->resolveInitialConversationId();
        $this->messageSearchHistory = session()->get($this->messageSearchHistorySessionKey(), []);
        $this->notificationMode = session()->get($this->notificationModeSessionKey(), 'all');
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
        $this->markConversationAsRead($conversationId);
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

    public function setNotificationMode(string $mode): void
    {
        abort_unless(in_array($mode, ['all', 'mentions', 'none'], true), 422);

        $this->notificationMode = $mode;
        session()->put($this->notificationModeSessionKey(), $mode);
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

        $conversation->participants()->sync(
            [$user->id => ['role' => 'admin']] + collect($participantIds)
                ->mapWithKeys(fn (int $participantId) => [$participantId => ['role' => 'member']])
                ->all()
        );

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

            $conversation->participants()->sync([
                $currentUser->id => ['role' => 'admin'],
                $otherUser->id => ['role' => 'member'],
            ]);
        }

        $this->selectConversation($conversation->id);
    }

    public function togglePing(): void
    {
        $this->isPingOpen = ! $this->isPingOpen;
        $this->selectedPingUserIds = [];
    }

    public function startGroupDirectConversation(array $userIds): void
    {
        $currentUser = auth()->user();

        abort_unless($currentUser instanceof User, 403);

        $userIds = collect($userIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id !== $currentUser->id)
            ->unique()
            ->values()
            ->toArray();

        abort_if(empty($userIds), 422, 'Pelo menos um utilizador deve ser selecionado.');

        // Para uma única pessoa, criar conversa direta
        if (count($userIds) === 1) {
            $this->startDirectConversation($userIds[0]);
            $this->isPingOpen = false;
            $this->selectedPingUserIds = [];
            return;
        }

        // Para múltiplas pessoas, criar grupo
        $participantIds = array_merge([$currentUser->id], $userIds);

        $conversation = ChatConversation::query()
            ->where('type', ChatConversation::TYPE_DIRECT)
            ->withCount('participants')
            ->get()
            ->first(function (ChatConversation $candidate) use ($participantIds) {
                if ($candidate->participants_count !== count($participantIds)) {
                    return false;
                }

                $candidateIds = $candidate->participants->pluck('id')->sort()->values()->toArray();
                $comparableIds = collect($participantIds)->sort()->values()->toArray();

                return $candidateIds === $comparableIds;
            });

        if (! $conversation) {
            $groupConversationName = User::query()
                ->whereIn('id', $userIds)
                ->orderBy('name')
                ->pluck('name')
                ->implode(', ');

            $conversation = ChatConversation::query()->create([
                'type' => ChatConversation::TYPE_DIRECT,
                'name' => Str::limit($groupConversationName !== '' ? $groupConversationName : 'Conversa de grupo', 255, ''),
                'avatar' => null,
                'created_by_id' => $currentUser->id,
            ]);

            $conversation->participants()->sync(
                collect($participantIds)
                    ->mapWithKeys(fn (int $participantId) => [
                        $participantId => ['role' => $participantId === $currentUser->id ? 'admin' : 'member'],
                    ])
                    ->all()
            );
        }

        $this->selectConversation($conversation->id);
        $this->isPingOpen = false;
        $this->selectedPingUserIds = [];
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

        if (! $conversation) {
            return;
        }

        abort_unless($this->canManageConversation($conversation, $user), 403);

        $validated = $this->validate([
            'roomInviteIds' => ['array'],
            'roomInviteIds.*' => ['integer', Rule::exists('users', 'id')],
        ]);

        $existingIds = $conversation->participants()->pluck('users.id')->all();
        $newIds = array_values(array_diff(array_unique(array_map('intval', $validated['roomInviteIds'] ?? [])), $existingIds));

        if ($newIds !== []) {
            $conversation->participants()->syncWithoutDetaching(
                collect($newIds)
                    ->mapWithKeys(fn (int $participantId) => [
                        $participantId => ['role' => 'member'],
                    ])
                    ->all()
            );
        }

        $this->roomInviteIds = [];
    }

    public function promoteConversationParticipant(int $userId): void
    {
        $currentUser = auth()->user();
        abort_unless($currentUser instanceof User, 403);

        $conversation = $this->currentConversation();

        if (! $conversation) {
            return;
        }

        abort_unless($this->canManageConversation($conversation, $currentUser), 403);

        if ($userId === $conversation->created_by_id) {
            return;
        }

        $conversation->participants()->updateExistingPivot($userId, ['role' => 'admin']);
    }

    public function removeConversationParticipant(int $userId): void
    {
        $currentUser = auth()->user();
        abort_unless($currentUser instanceof User, 403);

        $conversation = $this->currentConversation();

        if (! $conversation) {
            return;
        }

        abort_unless($this->canManageConversation($conversation, $currentUser), 403);

        if ($userId === $conversation->created_by_id) {
            return;
        }

        $conversation->participants()->detach($userId);
    }

    public function removeRoomParticipant(int $userId): void
    {
        $this->removeConversationParticipant($userId);
    }

    public function deleteDirectConversation(): void
    {
        $currentUser = auth()->user();
        abort_unless($currentUser instanceof User, 403);

        $conversation = $this->currentConversation();

        if (! $conversation) {
            return;
        }

        $isSingleDirectConversation = $conversation->isDirect() && $conversation->participants()->count() === 2;
        abort_unless($isSingleDirectConversation, 403);

        $deletedConversationId = $conversation->id;

        $this->clearTypingIndicator();
        $conversation->delete();

        if ((int) $this->selectedConversationId === (int) $deletedConversationId) {
            $this->selectedConversationId = $this->resolveInitialConversationId();
        }

        $this->messageBody = '';
        $this->messageAttachment = null;
        $this->replyingToMessageId = null;
        $this->roomInviteIds = [];
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
            $this->markConversationAsRead($conversation->id);

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

        if (! $conversation || ! $this->canManageConversation($conversation)) {
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
        $this->markConversationAsRead($this->selectedConversationId);

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
            ->with(['participants:id,name,email,estado,profile_photo_path'])
            ->first();
    }

    protected function canManageConversation(ChatConversation $conversation, ?User $user = null): bool
    {
        $user ??= auth()->user();

        if (! $user instanceof User) {
            return false;
        }

        if ($user->isAdmin() || (int) $conversation->created_by_id === (int) $user->id) {
            return true;
        }

        $participant = $conversation->participants->firstWhere('id', $user->id);

        return (string) ($participant?->pivot?->role ?? '') === 'admin';
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

    protected function notificationModeSessionKey(): string
    {
        return 'chat.notification_mode';
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

    protected function markConversationAsRead(?int $conversationId): void
    {
        $user = auth()->user();

        if (! $conversationId || ! $user instanceof User) {
            return;
        }

        $conversation = ChatConversation::query()
            ->whereKey($conversationId)
            ->whereHas('participants', fn ($query) => $query->where('users.id', $user->id))
            ->with('latestMessage')
            ->first();

        if (! $conversation || ! $conversation->latestMessage) {
            return;
        }

        $latestMessage = $conversation->latestMessage;

        if ((int) $latestMessage->user_id === (int) $user->id) {
            return;
        }

        $participant = $conversation->participants()->where('users.id', $user->id)->first();
        $lastReadAt = $participant?->pivot?->last_read_at;

        if ($lastReadAt && \Illuminate\Support\Carbon::parse($lastReadAt)->greaterThanOrEqualTo($latestMessage->created_at)) {
            return;
        }

        $conversation->participants()->updateExistingPivot($user->id, [
            'last_read_at' => now(),
        ]);
    }
}