<div wire:poll.6s class="h-[calc(100dvh-4rem)] overflow-hidden bg-gray-50 text-[#1e1e1e]">
    <div class="mx-auto flex h-full max-w-[1600px]">
        <aside class="hidden h-full w-20 flex-col border-r border-black/15 bg-gray-50 lg:flex">
            <div class="mt-auto flex justify-center px-2 pb-4">
                <img src="{{ asset('images/logo.png') }}" alt="Inovbooks" class="h-12 w-auto object-contain opacity-85">
            </div>
        </aside>

        <div class="grid h-full flex-1 grid-cols-1 lg:grid-cols-[minmax(0,1fr)_20rem]">
        <main class="relative flex h-full min-h-0 flex-col border-r border-black/10 bg-gray-50">
            @if ($selectedConversation)
                @php
                    $conversationParticipants = $selectedConversation->participants->where('id', '!=', auth()->id());
                    $roomConversations = $conversations->where('type', \App\Models\ChatConversation::TYPE_ROOM);
                @endphp

                <header class="pointer-events-none absolute inset-x-0 top-0 z-20 flex items-start justify-between gap-3 pl-1 pr-5 pt-3 lg:pl-2 lg:pr-8">
                    <div class="pointer-events-auto flex max-w-full flex-col gap-1">
                        @php
                            $chatTitle = $selectedConversation->isRoom()
                                ? ($selectedConversation->name ?: 'Sala')
                                : $selectedConversation->participants->firstWhere('id', '!=', auth()->id())?->name;
                        @endphp
                        <div class="inline-flex max-w-full items-center gap-2 rounded-lg bg-blue-800 px-3 py-1.5 text-sm font-semibold text-white">
                            <span class="truncate">{{ $chatTitle ?: ($selectedConversation->name ?: 'Conversa direta') }}</span>
                        </div>
                    </div>

                    <div class="pointer-events-auto flex items-center gap-2">
                        @if ($selectedConversation?->isRoom())
                            <button type="button" class="inline-flex size-9 items-center justify-center rounded-full border border-black/20 bg-white text-black/70 transition hover:bg-black/5" aria-label="Mais opcoes">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6h.01M12 12h.01M12 18h.01" />
                                </svg>
                            </button>
                        @endif
                        <button type="button" class="inline-flex size-9 items-center justify-center rounded-full border border-black/20 bg-white text-black/70 transition hover:bg-black/5" aria-label="Notificacoes">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                            </svg>
                        </button>
                    </div>
                </header>

                <section class="flex min-h-0 flex-1 flex-col overflow-hidden">
                    <div class="flex-1 overflow-y-auto px-4 py-10 pt-3 lg:px-8">
                        <div class="mx-auto max-w-4xl">
                            @php
                                $groupedMessages = $selectedConversation->messages->groupBy(fn ($message) => $message->created_at?->toDateString() ?? '');
                            @endphp

                            @forelse ($groupedMessages as $dateKey => $messages)
                                <div class="flex items-center gap-4 py-2">
                                    <div class="h-px flex-1 bg-black/10"></div>
                                    <div class="rounded-full bg-[#ececec] px-4 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-black/55 shadow-sm">
                                        {{ $dateKey !== '' ? \Illuminate\Support\Carbon::parse($dateKey)->locale('pt_PT')->translatedFormat('j \d\e F \d\e Y') : '' }}
                                    </div>
                                    <div class="h-px flex-1 bg-black/10"></div>
                                </div>

                                @php
                                    $orderedMessages = $messages->values();
                                @endphp

                                @foreach ($orderedMessages as $messageIndex => $message)
                                    @php
                                        $previousMessage = $messageIndex > 0 ? $orderedMessages[$messageIndex - 1] : null;
                                        $nextMessage = $messageIndex < $orderedMessages->count() - 1 ? $orderedMessages[$messageIndex + 1] : null;
                                        $hasPreviousSameSender = $previousMessage
                                            && (int) $previousMessage->user_id === (int) $message->user_id;
                                        $hasNextSameSender = $nextMessage
                                            && (int) $nextMessage->user_id === (int) $message->user_id;
                                        $isMessageContinuation = $hasPreviousSameSender;
                                        $hasPreviousSameSenderSameHour = $isMessageContinuation
                                            && $previousMessage->created_at
                                            && $message->created_at
                                            && $previousMessage->created_at->format('Y-m-d H') === $message->created_at->format('Y-m-d H');
                                        $hasNextSameSenderSameHour = $hasNextSameSender
                                            && $nextMessage->created_at
                                            && $message->created_at
                                            && $nextMessage->created_at->format('Y-m-d H') === $message->created_at->format('Y-m-d H');
                                        $shouldShowMessageTime = ! $hasNextSameSenderSameHour;
                                        $isOwnMessage = (int) $message->user_id === (int) auth()->id();
                                        $messageUserName = $message->user?->name ?? 'Utilizador';
                                        $messageUserPhoto = $message->user?->profile_photo_url;
                                        $messageReactions = $message->reactions->groupBy('emoji');
                                        $currentUserReaction = $message->reactions->firstWhere('user_id', auth()->id())?->emoji;
                                        $hasNextMessage = $nextMessage !== null;
                                        $isGroupedMessage = $hasPreviousSameSender || $hasNextSameSender;
                                        $messageTopSpacingClass = $isMessageContinuation
                                            ? 'mt-0'
                                            : 'mt-3';
                                        $messageBottomSpacingClass = $messageReactions->isNotEmpty() && $hasNextMessage
                                            ? 'mb-5'
                                            : '';
                                        $messageBubblePaddingClass = $isGroupedMessage
                                            ? ($messageReactions->isNotEmpty() ? 'pt-0.5 pb-2' : 'py-0.5')
                                            : ($messageReactions->isNotEmpty() ? 'pt-2 pb-2' : 'py-2');
                                    @endphp
                                    <div x-data="{ openReactionPicker: false }" class="group flex {{ $isOwnMessage ? 'justify-end' : 'justify-start' }} {{ $messageTopSpacingClass }} {{ $messageBottomSpacingClass }}">
                                        <div class="flex max-w-[min(92%,64rem)] items-start {{ $isOwnMessage ? 'flex-row-reverse' : '' }} gap-1.5">
                                            <div class="mt-0.5 flex shrink-0 flex-col items-center">
                                                @if ($isMessageContinuation)
                                                    <div class="size-8"></div>
                                                @else
                                                    <div class="flex size-8 items-center justify-center overflow-hidden rounded-full border border-black/10 bg-white text-[11px] font-semibold text-black/70 shadow-sm ring-2 ring-white">
                                                        @if ($messageUserPhoto)
                                                            <img src="{{ $messageUserPhoto }}" alt="{{ $messageUserName }}" class="size-full object-cover">
                                                        @else
                                                            <span>{{ mb_strtoupper(mb_substr($messageUserName, 0, 1)) }}</span>
                                                        @endif
                                                    </div>
                                                @endif
                                                @if ($shouldShowMessageTime)
                                                    <span class="mt-1 text-[10px] leading-none text-black/45 {{ $hasPreviousSameSenderSameHour ? 'opacity-90' : '' }}">{{ $message->created_at->format('H:i') }}</span>
                                                @endif
                                            </div>

                                            <div class="relative w-fit min-w-[10rem] max-w-full px-4 {{ $messageBubblePaddingClass }} text-[#1f1f1f] {{ $isOwnMessage ? 'bg-[#dbeafe]' : 'bg-[#ececec]' }} shadow-[0_1px_1px_rgba(0,0,0,0.02)] {{ $hasPreviousSameSender && $hasNextSameSender ? 'rounded-md' : ($hasPreviousSameSender ? 'rounded-t-md rounded-b-xl' : ($hasNextSameSender ? 'rounded-t-xl rounded-b-md' : 'rounded-xl')) }}">
                                                <div class="mb-1 flex items-center gap-2 text-xs {{ $isOwnMessage ? 'justify-end' : '' }}">
                                                    @if ($selectedConversation?->isRoom())
                                                        <span class="font-semibold text-slate-900">{{ $messageUserName }}</span>
                                                    @endif
                                                </div>

                                                @if ($message->repliedToMessage)
                                                    @php
                                                        $repliedMessageAuthor = $message->repliedToMessage->user?->name ?? 'Utilizador';
                                                        $repliedMessagePreview = trim((string) $message->repliedToMessage->body) !== ''
                                                            ? \Illuminate\Support\Str::limit($message->repliedToMessage->body, 140)
                                                            : ($message->repliedToMessage->attachment_name ?: 'Anexo');
                                                    @endphp

                                                    <div class="mb-2 rounded-lg bg-white/40 px-3 py-2 text-left ring-1 ring-black/5">
                                                        <div class="flex gap-2">
                                                            <div class="border-l-2 border-blue-300"></div>
                                                            <div class="min-w-0 flex-1">
                                                                <p class="truncate text-[11px] font-semibold text-blue-800">{{ $repliedMessageAuthor }}</p>
                                                                <p class="mt-0.5 line-clamp-2 text-sm leading-5 text-black/70">{{ $repliedMessagePreview }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (trim((string) $message->body) !== '')
                                                    <p class="whitespace-pre-wrap text-[15px] leading-6">{{ $message->body }}</p>
                                                @endif

                                                @if ($message->attachment_path)
                                                    @php
                                                        $attachmentUrl = \Illuminate\Support\Facades\Storage::url($message->attachment_path);
                                                        $attachmentMime = (string) ($message->attachment_mime ?? '');
                                                        $attachmentExtension = \Illuminate\Support\Str::lower(pathinfo((string) ($message->attachment_name ?? ''), PATHINFO_EXTENSION));
                                                        $isImageAttachment = str_starts_with($attachmentMime, 'image/')
                                                            || in_array($attachmentExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'], true);
                                                    @endphp

                                                    @if ($isImageAttachment)
                                                        <a href="{{ $attachmentUrl }}" target="_blank" rel="noopener noreferrer" class="mt-2 block overflow-hidden rounded-xl border border-black/10 bg-white/70">
                                                            <img src="{{ $attachmentUrl }}" alt="{{ $message->attachment_name ?: 'Imagem anexada' }}" class="max-h-64 w-full object-cover">
                                                        </a>
                                                    @endif

                                                    @unless ($isImageAttachment)
                                                        <a href="{{ $attachmentUrl }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-flex items-center gap-2 rounded-lg border border-black/10 bg-white/80 px-2.5 py-1.5 text-xs font-semibold text-black/70 transition hover:bg-white">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5v-9m0 9-3-3m3 3 3-3M3.75 15.75v1.5A2.25 2.25 0 0 0 6 19.5h12a2.25 2.25 0 0 0 2.25-2.25v-1.5" />
                                                            </svg>
                                                            <span class="max-w-[14rem] truncate">{{ $message->attachment_name ?: 'Anexo' }}</span>
                                                        </a>
                                                    @endunless
                                                @endif

                                                <div class="absolute -bottom-4 {{ $isOwnMessage ? 'right-4' : 'left-4' }} z-20 flex flex-nowrap items-center gap-2">
                                                    @foreach ($messageReactions as $emoji => $reactions)
                                                        @php
                                                            $reactingUser = $reactions->first()?->user;
                                                            $reactingUserName = $reactingUser?->name ?? 'Utilizador';
                                                            $reactingUserPhoto = $reactingUser?->profile_photo_url;
                                                            $reactionCount = $reactions->count();
                                                        @endphp

                                                        <button type="button" wire:click="toggleReaction({{ $message->id }}, @js($emoji))" class="inline-flex items-center rounded-full bg-white shadow-sm {{ $selectedConversation?->isDirect() ? 'px-2.5 py-1' : 'gap-1' }} {{ $currentUserReaction === $emoji ? 'border border-blue-300' : 'border border-black/10' }}">
                                                            @unless ($selectedConversation?->isDirect())
                                                                <span class="flex size-5 shrink-0 items-center justify-center overflow-hidden rounded-full bg-white">
                                                                    @if ($reactingUserPhoto)
                                                                        <img src="{{ $reactingUserPhoto }}" alt="{{ $reactingUserName }}" class="block size-full object-cover">
                                                                    @else
                                                                        <span>{{ mb_strtoupper(mb_substr($reactingUserName, 0, 1)) }}</span>
                                                                    @endif
                                                                </span>
                                                            @endunless
                                                            <span class="text-sm leading-none {{ $selectedConversation?->isDirect() ? '' : 'pr-1.5 py-1' }}">{{ $emoji }}</span>
                                                            @if ($reactionCount > 1)
                                                                <span class="text-[11px] font-semibold leading-none text-black/60">{{ $reactionCount }}</span>
                                                            @endif
                                                        </button>
                                                    @endforeach
                                                </div>

                                                    <div class="absolute top-1/2 {{ $isOwnMessage ? '-left-[4.75rem] -translate-y-1/2' : '-right-[4.75rem] -translate-y-1/2' }} z-20 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                                        <button type="button" wire:click="replyToMessage({{ $message->id }})" class="inline-flex size-7 items-center justify-center rounded-full border border-black/10 bg-white text-black/80 shadow-sm" aria-label="Responder à mensagem">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h13a5 5 0 0 1 5 5v1" />
                                                            </svg>
                                                        </button>

                                                        <button type="button" @click="openReactionPicker = !openReactionPicker" class="inline-flex size-7 items-center justify-center rounded-full border border-black/10 bg-white text-[11px] font-bold text-black/80 shadow-sm" aria-label="Reagir à mensagem">
                                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <defs>
                                                                    <mask id="add-reaction-mask">
                                                                        <rect x="0" y="0" width="24" height="24" fill="white" />
                                                                        <circle cx="19" cy="19" r="4.5" fill="black" />
                                                                    </mask>
                                                                </defs>

                                                            <g transform="translate(0.7 -0.2)" mask="url(#add-reaction-mask)" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <circle cx="12" cy="12" r="10" />
                                                                <path d="M8 14s1.5 2 4 2 4-2 4-2" />
                                                                <line x1="9" y1="9" x2="9.01" y2="9"></line>
                                                                <line x1="15" y1="9" x2="15.01" y2="9"></line>
                                                            </g>

                                                            <g stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                                <line x1="19" y1="16" x2="19" y2="22" />
                                                                <line x1="16" y1="19" x2="22" y2="19" />
                                                            </g>
                                                        </svg>
                                                    </button>

                                                    <div x-cloak x-show="openReactionPicker" @click.outside="openReactionPicker = false" class="absolute bottom-8 {{ $isOwnMessage ? 'right-0' : 'left-0' }} z-30 flex items-center gap-1 rounded-full border border-black/10 bg-white p-1 shadow-lg">
                                                        @foreach ($reactionEmojis as $reactionEmoji)
                                                            <button type="button" wire:click="toggleReaction({{ $message->id }}, @js($reactionEmoji))" @click="openReactionPicker = false" class="inline-flex size-8 items-center justify-center rounded-full text-base transition hover:bg-black/5" aria-label="Reagir com {{ $reactionEmoji }}">
                                                                {{ $reactionEmoji }}
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @empty
                                <div class="flex min-h-[360px] items-center justify-center">
                                    <div class="rounded-2xl border border-dashed border-black/20 bg-white px-10 py-8 text-center">
                                        <p class="text-lg font-semibold text-black/70">A conversa está vazia</p>
                                        <p class="mt-2 text-sm text-black/45">Escreve a primeira mensagem para iniciar.</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 lg:px-8">
                        @if ($replyingToMessageId)
                            @php
                                $replyingMessage = $selectedConversation?->messages->firstWhere('id', $replyingToMessageId);
                            @endphp
                            @if ($replyingMessage)
                                <div class="mx-auto mb-2 max-w-4xl rounded-xl border border-black/10 bg-white px-4 py-3 shadow-sm">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0 border-l-2 border-blue-300 pl-3">
                                            <p class="text-xs font-semibold text-blue-800">
                                                {{ $replyingMessage->user?->name ?? 'Utilizador' }}
                                            </p>
                                            <p class="mt-1 line-clamp-2 text-sm text-black/65">
                                                {{ \Illuminate\Support\Str::limit(trim((string) $replyingMessage->body) !== '' ? $replyingMessage->body : ($replyingMessage->attachment_name ?: 'Anexo'), 120) }}
                                            </p>
                                        </div>
                                        <button type="button" wire:click="$set('replyingToMessageId', null)" class="text-xs font-semibold text-black/45 transition hover:text-black/70">Cancelar</button>
                                    </div>
                                </div>
                            @endif
                        @endif

                        @if ($typingParticipants->isNotEmpty())
                            @php
                                $typingNames = $typingParticipants->pluck('name')->join(', ', ' e ');
                                $typingVerb = $typingParticipants->count() === 1 ? 'está' : 'estão';
                            @endphp
                            <div class="mx-auto mb-1 max-w-4xl px-2 text-xs font-medium text-blue-800">
                                <span class="inline-flex items-center gap-2">
                                    <span class="size-1.5 shrink-0 rounded-full bg-blue-600 animate-pulse"></span>
                                    <span class="truncate">{{ $typingNames }} {{ $typingVerb }} a escrever...</span>
                                </span>
                            </div>
                        @endif

                        <form wire:submit.prevent="sendMessage" class="mx-auto flex max-w-4xl flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <button type="button" wire:click="toggleMessageSearch" class="inline-flex size-9 shrink-0 items-center justify-center rounded-full border transition {{ $isMessageSearchMode ? 'border-blue-300 bg-blue-100 text-blue-800' : 'border-black/20 bg-white text-slate-900 hover:bg-blue-100 hover:text-blue-800 hover:border-blue-300' }}" aria-label="Pesquisar mensagens">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2.3" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                                    </svg>
                                </button>

                            <div x-data="{ openEmoji: false, emojiSearch: '', activeEmojiCategory: 'faces', emojiCategories: { faces: [{ value: '😀', label: 'feliz sorriso' }, { value: '😁', label: 'sorriso grande' }, { value: '😂', label: 'rir lagrimas' }, { value: '🤣', label: 'rir muito' }, { value: '😊', label: 'timido feliz' }, { value: '😍', label: 'apaixonado' }, { value: '😎', label: 'cool oculos' }, { value: '🤔', label: 'pensativo' }, { value: '😢', label: 'triste' }, { value: '😭', label: 'chorar' }, { value: '😡', label: 'zangado' }, { value: '🥳', label: 'festa' }], gestures: [{ value: '👍', label: 'gosto positivo' }, { value: '👎', label: 'nao gosto negativo' }, { value: '👏', label: 'palmas' }, { value: '🙌', label: 'celebrar' }, { value: '🙏', label: 'obrigado pedido' }, { value: '👌', label: 'ok perfeito' }, { value: '🤝', label: 'acordo' }, { value: '👋', label: 'ola adeus' }, { value: '💪', label: 'forca' }, { value: '✌️', label: 'paz' }], objects: [{ value: '🔥', label: 'fogo quente' }, { value: '🎉', label: 'celebracao' }, { value: '⭐', label: 'estrela destaque' }, { value: '💡', label: 'ideia' }, { value: '📚', label: 'livros estudo' }, { value: '💬', label: 'mensagem conversa' }, { value: '✅', label: 'confirmado' }, { value: '❗', label: 'importante' }, { value: '⚠️', label: 'aviso' }, { value: '💯', label: '100%' }], hearts: [{ value: '❤️', label: 'coracao vermelho amor' }, { value: '🧡', label: 'coracao laranja' }, { value: '💛', label: 'coracao amarelo' }, { value: '💚', label: 'coracao verde' }, { value: '💙', label: 'coracao azul' }, { value: '💜', label: 'coracao roxo' }, { value: '🤍', label: 'coracao branco' }, { value: '🖤', label: 'coracao preto' }, { value: '💔', label: 'coracao partido' }, { value: '💕', label: 'dois coracoes' }] }, addEmoji(emoji) { const input = this.$refs.messageInput; input.value = (input.value || '') + emoji.value; input.dispatchEvent(new Event('input', { bubbles: true })); input.focus(); this.openEmoji = false; }, filteredEmojis() { const source = this.emojiCategories[this.activeEmojiCategory] || []; const query = this.emojiSearch.trim().toLowerCase(); if (!query) return source; return source.filter((emoji) => emoji.label.includes(query)); } }" class="flex w-full rounded-full border border-black/20 bg-white pl-4 pr-1 py-1 focus-within:border-blue-300 focus-within:ring-1 focus-within:ring-blue-300"> 
                                @if ($isMessageSearchMode)
                                    <label for="message-search" class="sr-only">Pesquisar mensagens</label>
                                    <input id="message-search" wire:model.live.debounce.250ms="messageSearch" type="text" class="w-full align-center resize-none border-none bg-transparent py-1 px-0 text-sm text-black/80 placeholder:text-black/40 focus:outline-none focus:ring-0" placeholder="Pesquisar mensagens nesta conversa...">
                                @else
                                    <label for="message-body" class="sr-only">Mensagem</label>
                                    <input id="message-body" x-ref="messageInput" wire:model.live.debounce.300ms="messageBody" rows="1" class="w-full align-center resize-none border-none bg-transparent p-0 text-sm text-black/80 placeholder:text-black/40 focus:outline-none focus:ring-0" placeholder="Escreve uma mensagem..."></input>
                                    <input type="file" wire:model="messageAttachment" x-ref="chatAttachmentInput" class="hidden" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.txt,.csv,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar">
                                @endif
                            
                                <div class="relative flex items-center gap-3">
                                    @unless ($isMessageSearchMode)
                                    <button type="button" @click="$refs.chatAttachmentInput.click()" class="inline-flex size-7 items-center justify-center rounded-full text-black/65 transition hover:text-black/80" aria-label="Anexar ficheiro">
                                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path>
                                        </svg>
                                    </button>

                                    <button type="button" @click="openEmoji = !openEmoji" class="inline-flex size-7 items-center justify-center rounded-full text-black/65 transition hover:text-black/80" aria-label="Abrir emojis">
                                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
                                            <line x1="9" y1="9" x2="9.01" y2="9"></line>
                                            <line x1="15" y1="9" x2="15.01" y2="9"></line>
                                        </svg>
                                    </button>

                                    <div x-cloak x-show="openEmoji" @click.outside="openEmoji = false" class="absolute bottom-10 right-10 z-20 w-60 rounded-2xl border border-black/10 bg-white p-2 shadow-lg">
                                        <input type="text" x-model="emojiSearch" placeholder="Pesquisar emoji" class="mb-2 w-full rounded-lg border border-black/10 px-2 py-1 text-xs text-black/70 placeholder:text-black/35 focus:border-blue-300 focus:outline-none focus:ring-1 focus:ring-blue-300">

                                        <div class="mb-2 flex items-center gap-1">
                                            <button type="button" @click="activeEmojiCategory = 'faces'" :class="activeEmojiCategory === 'faces' ? 'bg-blue-800 text-white' : 'bg-black/5 text-black/70 hover:bg-black/10'" class="rounded-md px-2 py-1 text-[11px] font-semibold">Caras</button>
                                            <button type="button" @click="activeEmojiCategory = 'gestures'" :class="activeEmojiCategory === 'gestures' ? 'bg-blue-800 text-white' : 'bg-black/5 text-black/70 hover:bg-black/10'" class="rounded-md px-2 py-1 text-[11px] font-semibold">Mãos</button>
                                            <button type="button" @click="activeEmojiCategory = 'objects'" :class="activeEmojiCategory === 'objects' ? 'bg-blue-800 text-white' : 'bg-black/5 text-black/70 hover:bg-black/10'" class="rounded-md px-2 py-1 text-[11px] font-semibold">Objetos</button>
                                            <button type="button" @click="activeEmojiCategory = 'hearts'" :class="activeEmojiCategory === 'hearts' ? 'bg-blue-800 text-white' : 'bg-black/5 text-black/70 hover:bg-black/10'" class="rounded-md px-2 py-1 text-[11px] font-semibold">Corações</button>
                                        </div>

                                        <div class="max-h-36 overflow-y-auto">
                                            <div class="grid grid-cols-6 gap-1">
                                                <template x-for="emoji in filteredEmojis()" :key="emoji.value">
                                                    <button type="button" @click="addEmoji(emoji)" class="inline-flex h-7 w-7 items-center justify-center rounded-md text-base transition hover:bg-black/5" :title="emoji.label" x-text="emoji.value"></button>
                                                </template>
                                            </div>

                                            <p x-show="filteredEmojis().length === 0" class="px-1 py-2 text-xs text-black/45">Sem resultados.</p>
                                        </div>
                                    </div>
                                    @endunless

                                    <button type="submit" class="inline-flex size-7 items-center justify-center rounded-full bg-blue-800 text-white transition hover:bg-blue-900" aria-label="Enviar mensagem">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="2.3" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 17V7m0 0-4 4m4-4 4 4" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            @if (! $isMessageSearchMode && $messageAttachment)
                                @if (str_starts_with((string) ($messageAttachment->getMimeType() ?? ''), 'image/'))
                                    <div class="group relative h-24 w-20 overflow-hidden rounded-2xl border border-blue-200 bg-white p-1 shadow-sm">
                                        <img src="{{ $messageAttachment->temporaryUrl() }}" alt="Pré-visualização do anexo" class="h-full w-full rounded-xl object-cover">
                                        <button type="button" wire:click="removeAttachment" class="absolute right-1.5 top-1.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-black/75 text-[11px] font-bold leading-none text-white opacity-0 transition group-hover:opacity-100 focus:opacity-100" aria-label="Remover imagem anexada">
                                            x
                                        </button>
                                    </div>
                                @else
                                    <div class="inline-flex max-w-[18rem] items-center gap-2 rounded-full border border-black/15 bg-white px-3 py-1.5 text-xs text-black/70 shadow-sm">
                                        <span class="truncate">{{ $messageAttachment->getClientOriginalName() }}</span>
                                        <button type="button" wire:click="removeAttachment" class="rounded-full bg-black/5 px-2 py-0.5 text-[11px] font-semibold text-black/60 transition hover:bg-black/10 hover:text-black/80">remover</button>
                                    </div>
                                @endif
                            @endif
                        </form>
                        @error('messageBody') <p class="mx-auto mt-1 max-w-4xl text-xs text-rose-600">{{ $message }}</p> @enderror
                        @error('messageAttachment') <p class="mx-auto mt-1 max-w-4xl text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </section>
            @else
                <div class="flex flex-1 items-center justify-center px-8 py-16">
                    <div class="rounded-2xl border border-black/10 bg-white px-10 py-10 text-center shadow-sm">
                        <h2 class="text-2xl font-semibold text-black/80">Escolhe uma conversa para começar</h2>
                        <p class="mt-2 text-sm text-black/45">Seleciona uma pessoa ou uma sala no painel direito.</p>
                    </div>
                </div>
            @endif
        </main>

        <aside class="hidden h-full min-h-0 flex-col bg-gray-50 lg:flex">
            @if ($isMessageSearchMode)
                <div class="border-b border-black/10 px-4 py-4">
                    <div class="mb-3 flex items-center justify-between gap-2">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-black/35">Histórico de pesquisa</p>
                        <button type="button" wire:click="clearMessageSearchHistory" class="rounded-full border border-black/15 bg-white px-3 py-1 text-xs font-semibold text-black/65 transition hover:bg-black/5">Limpar</button>
                    </div>

                    <div class="max-h-[calc(100dvh-14rem)] space-y-2 overflow-y-auto">
                        @forelse ($messageSearchHistory as $term)
                            <button type="button" wire:click="searchFromHistory(@js($term))" class="w-full rounded-full border border-black/15 bg-white px-3 py-1.5 text-left text-sm text-black/75 transition hover:bg-black/5">{{ $term }}</button>
                        @empty
                            <p class="text-xs text-black/45">Sem pesquisas recentes</p>
                        @endforelse
                    </div>
                </div>
            @else
            <div class="border-b border-black/10 px-4 py-4">
                @php
                    $directConversations = $conversations->where('type', \App\Models\ChatConversation::TYPE_DIRECT)->take(8);
                @endphp

                <div class="flex items-center gap-2 overflow-x-auto pb-1">
                    @forelse ($directConversations as $directConversation)
                        @php
                            $directParticipant = $directConversation->participants->firstWhere('id', '!=', auth()->id());
                            $directParticipantName = $directParticipant?->name ?? $directConversation->name ?? 'Conversa direta';
                            $directParticipantPhoto = $directParticipant?->profile_photo_url;
                            $isDirectConversationUnread = $directConversation->latestMessage
                                && (int) $directConversation->latestMessage->user_id !== (int) auth()->id()
                                && (int) $selectedConversationId !== (int) $directConversation->id;
                        @endphp

                        <button type="button" wire:click="selectConversation({{ $directConversation->id }})" class="min-w-[3.2rem] text-center">
                            <div class="mx-auto inline-flex size-9 items-center justify-center overflow-hidden rounded-full border {{ $isDirectConversationUnread ? 'border-blue-800' : 'border-black/15' }} bg-white text-xs font-semibold {{ $isDirectConversationUnread ? 'text-blue-800' : 'text-black/70' }}">
                                @if ($directParticipantPhoto)
                                    <img src="{{ $directParticipantPhoto }}" alt="{{ $directParticipantName }}" class="size-full object-cover">
                                @else
                                    {{ mb_strtoupper(mb_substr($directParticipantName, 0, 1)) }}
                                @endif
                            </div>
                            <p class="truncate text-[11px] {{ $isDirectConversationUnread ? 'text-blue-800' : 'text-black/55' }}">{{ \Illuminate\Support\Str::limit($directParticipantName, 8) }}</p>
                            @if ($isDirectConversationUnread)
                                <span class="mx-auto mt-1 block size-1 rounded-full bg-blue-800"></span>
                            @endif
                        </button>
                    @empty
                        <p class="text-xs text-black/45">Sem mensagens diretas</p>
                    @endforelse
                </div>
            </div>

            <div class="border-b border-black/10 px-4 py-4">
                <label for="user-search" class="text-xs font-semibold uppercase tracking-[0.16em] text-black/35">Mensagens diretas</label>
                <input id="user-search" wire:model.live.debounce.250ms="userSearch" type="text" placeholder="Pesquisar pessoa" class="mt-2 w-full rounded-full border border-black/20 bg-white px-3 py-2 text-xs text-black/75 placeholder:text-black/40 focus:border-black/30 focus:ring-0">

                <div class="mt-3 max-h-44 space-y-2 overflow-y-auto">
                    @forelse ($availableUsers->take(10) as $user)
                        <button type="button" wire:click="startDirectConversation({{ $user->id }})" class="flex w-full items-center gap-2 rounded-full border border-black/20 bg-white px-3 py-1.5 text-left text-sm text-black/75 transition hover:bg-black/5">
                            <span class="inline-flex size-6 items-center justify-center rounded-full bg-[#ecece8] text-[10px] font-semibold">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                            <span class="truncate">{{ $user->name }}</span>
                        </button>
                    @empty
                        <p class="text-xs text-black/45">Sem utilizadores</p>
                    @endforelse
                </div>
            </div>

            <div class="px-4 py-4">
                <div class="mb-3 flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-black/35">Salas</p>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-black/45">{{ $conversations->where('type', \App\Models\ChatConversation::TYPE_ROOM)->count() }}</span>
                        @if (auth()->user()?->isAdmin())
                            <button type="button" wire:click="toggleRoomForm" class="inline-flex size-7 items-center justify-center rounded-full border border-black/20 bg-white text-black/70 transition hover:bg-black/5" aria-label="Nova sala">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>

                <div class="max-h-48 space-y-2 overflow-y-auto">
                    @forelse ($conversations->where('type', \App\Models\ChatConversation::TYPE_ROOM) as $roomConversation)
                        <button type="button" wire:click="selectConversation({{ $roomConversation->id }})" class="w-full rounded-full border px-3 py-1.5 text-left text-sm transition {{ (int) $selectedConversationId === (int) $roomConversation->id ? 'border-black bg-white font-semibold text-black/85' : 'border-black/20 bg-transparent text-black/65 hover:bg-white' }}">
                            {{ $roomConversation->name ?: 'Sala' }}
                        </button>
                    @empty
                        <p class="text-xs text-black/45">Sem salas disponiveis</p>
                    @endforelse
                </div>

                @if ($showRoomForm && auth()->user()?->isAdmin())
                    <div class="mt-4 rounded-2xl border border-black/15 bg-white p-3">
                        <h2 class="text-sm font-semibold text-black/80">Criar sala</h2>
                        <div class="mt-2 space-y-2">
                            <input wire:model.defer="roomName" type="text" class="w-full rounded-xl border border-black/20 bg-white px-3 py-2 text-sm text-black/75 placeholder:text-black/40 focus:border-black/30 focus:ring-0" placeholder="Nome da sala">
                            @error('roomName') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror

                            <input wire:model.defer="roomAvatar" type="text" class="w-full rounded-xl border border-black/20 bg-white px-3 py-2 text-sm text-black/75 placeholder:text-black/40 focus:border-black/30 focus:ring-0" placeholder="Avatar (url ou simbolo)">
                            @error('roomAvatar') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror

                            <div class="max-h-24 space-y-1 overflow-y-auto rounded-xl border border-black/10 p-2">
                                @foreach ($availableUsers as $user)
                                    <label class="flex items-center gap-2 rounded-lg px-2 py-1 hover:bg-black/5">
                                        <input wire:model.defer="roomParticipantIds" type="checkbox" value="{{ $user->id }}" class="rounded border-black/20 text-black focus:ring-black/20">
                                        <span class="truncate text-xs text-black/70">{{ $user->name }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <button type="button" wire:click="createRoom" class="w-full rounded-full bg-black px-3 py-2 text-xs font-semibold text-white transition hover:bg-black/80">Criar sala</button>
                        </div>
                    </div>
                @endif

                @if ($selectedConversation && $selectedConversation->isRoom() && (auth()->user()->isAdmin() || $selectedConversation->created_by_id === auth()->id()))
                    <div class="mt-4 rounded-2xl border border-black/15 bg-white p-3">
                        <p class="text-xs font-semibold text-black/70">Convidar para sala</p>
                        <div class="mt-2 max-h-24 space-y-1 overflow-y-auto">
                            @forelse ($roomInviteCandidates as $candidate)
                                <label class="flex items-center gap-2 rounded-lg px-2 py-1 hover:bg-black/5">
                                    <input wire:model.defer="roomInviteIds" type="checkbox" value="{{ $candidate->id }}" class="rounded border-black/20 text-black focus:ring-black/20">
                                    <span class="truncate text-xs text-black/70">{{ $candidate->name }}</span>
                                </label>
                            @empty
                                <p class="text-xs text-black/45">Sem candidatos</p>
                            @endforelse
                        </div>
                        <button type="button" wire:click="inviteSelectedUsers" class="mt-2 w-full rounded-full bg-black px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-black/80">Convidar</button>
                    </div>
                @endif
            </div>
            @endif
        </aside>
        </div>
    </div>
</div>
