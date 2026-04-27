<aside class="hidden h-full min-h-0 flex-col bg-gray-50 lg:flex overflow-y-auto max-h-screen">
    @if ($isMessageSearchMode)
        <div class="border-b border-black/10 px-4 py-4">
            <div class="mb-3 flex items-center justify-between gap-2">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-black/35">Histórico de pesquisa</p>
                <button type="button" wire:click="clearMessageSearchHistory" class="rounded-full border border-black/15 bg-white px-3 py-1 text-xs font-semibold text-black/65 transition hover:bg-blue-50 hover:border-blue-300">Limpar</button>
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
                $buildParticipantInitials = function (?string $name): string {
                    $cleanName = trim((string) $name);

                    if ($cleanName === '') {
                        return 'U';
                    }

                    $parts = collect(preg_split('/\s+/', $cleanName) ?: [])
                        ->filter(fn ($part) => trim((string) $part) !== '')
                        ->values();

                    if ($parts->count() === 1) {
                        return mb_strtoupper(mb_substr((string) $parts[0], 0, 2));
                    }

                    return mb_strtoupper(mb_substr((string) $parts->first(), 0, 1) . mb_substr((string) $parts->last(), 0, 1));
                };

                $dmSearchTerm = mb_strtolower(trim($directSearch));
                $directConversations = $conversations
                    ->where('type', \App\Models\ChatConversation::TYPE_DIRECT)
                    ->filter(function ($conversation) use ($dmSearchTerm, $buildParticipantInitials) {
                        if ($dmSearchTerm === '') {
                            return true;
                        }

                        $otherParticipants = $conversation->participants->where('id', '!=', auth()->id())->values();
                        $singleParticipantName = $otherParticipants->first()?->name ?? '';
                        $participantsLabel = $otherParticipants->pluck('name')->implode(' ');

                        $groupInitials = $otherParticipants
                            ->take(2)
                            ->map(fn ($participant) => $buildParticipantInitials($participant?->name))
                            ->implode('+');

                        if ($otherParticipants->count() > 2) {
                            $groupInitials .= '+' . ($otherParticipants->count() - 2);
                        }

                        $searchableText = mb_strtolower(implode(' ', array_filter([
                            $singleParticipantName,
                            (string) $conversation->name,
                            $participantsLabel,
                            $groupInitials,
                        ])));

                        return str_contains($searchableText, $dmSearchTerm);
                    })
                    ->take(8);
            @endphp

            @if ($isPingOpen)
                <div x-data="{ pingSearch: '', selectedPingUsers: @js($selectedPingUserIds ?? []), availablePingUsers: @js($availableUsers->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'photo' => $u->profile_photo_url])->values()) }" class="flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <button type="button" wire:click="togglePing" class="inline-flex size-8 items-center justify-center rounded-full border border-black/20 bg-white text-black/70 hover:bg-blue-50 hover:text-blue-800 hover:border-blue-300 transition" aria-label="Voltar">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button type="button" wire:click="startGroupDirectConversation(selectedPingUsers)" :disabled="selectedPingUsers.length === 0" class="inline-flex size-8 items-center justify-center rounded-full bg-blue-800 text-white hover:bg-blue-900 transition disabled:opacity-50 disabled:cursor-not-allowed" aria-label="Confirmar">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </button>
                    </div>

                    <div class="relative mb-4">
                        <div class="rounded-xl border border-black/20 bg-white p-2 min-h-[60px]">
                            <div class="flex flex-wrap items-center gap-2">
                                <template x-for="userId in selectedPingUsers" :key="userId">
                                    <div class="inline-flex items-center gap-2 rounded-full bg-white border border-blue-300 px-2.5 py-1 text-xs text-black/85">
                                        <template x-if="availablePingUsers.find(u => u.id == userId)?.photo">
                                            <img :src="availablePingUsers.find(u => u.id == userId)?.photo" :alt="availablePingUsers.find(u => u.id == userId)?.name" class="size-5 rounded-full object-cover">
                                        </template>
                                        <template x-if="!availablePingUsers.find(u => u.id == userId)?.photo">
                                            <span class="inline-flex size-5 items-center justify-center rounded-full bg-[#dce6f7] text-[10px] font-semibold text-[#7f97d9]" x-text="(availablePingUsers.find(u => u.id == userId)?.name || 'U').slice(0, 1).toUpperCase()"></span>
                                        </template>
                                        <span x-text="availablePingUsers.find(u => u.id == userId)?.name || 'Utilizador'" class="font-medium leading-none"></span>
                                        <button type="button" @click="selectedPingUsers = selectedPingUsers.filter(id => id != userId)" class="inline-flex size-4 items-center justify-center rounded-full bg-black/30 text-white transition hover:bg-black/45" aria-label="Remover utilizador">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </template>

                                <input
                                    type="text"
                                    x-model="pingSearch"
                                    @focus="pingSearch = pingSearch"
                                    placeholder="Escreve para pesquisar pessoas..."
                                    class="min-w-[11rem] flex-1 border-none bg-transparent px-2 py-1 text-xs text-black/75 placeholder:text-black/40 focus:outline-none focus:ring-0"
                                >
                            </div>
                        </div>

                        <div x-cloak x-show="pingSearch.trim() !== '' && availablePingUsers.filter(u => u.name.toLowerCase().includes(pingSearch.toLowerCase()) && !selectedPingUsers.includes(u.id)).length > 0" class="absolute inset-x-0 top-full z-10 mt-2 max-h-48 space-y-2 overflow-y-auto rounded-xl border border-black/15 bg-white p-1 shadow-lg">
                            <template x-for="user in availablePingUsers.filter(u => u.name.toLowerCase().includes(pingSearch.toLowerCase()) && !selectedPingUsers.includes(u.id))" :key="user.id">
                                <button type="button" @click="selectedPingUsers.push(user.id); pingSearch = ''" class="flex w-full items-center gap-2 rounded-lg bg-white px-3 py-1 text-left text-xs transition hover:bg-black/5">
                                    <template x-if="user.photo">
                                        <img :src="user.photo" :alt="user.name" class="size-6 rounded-full object-cover">
                                    </template>
                                    <template x-if="!user.photo">
                                        <span class="inline-flex size-8 items-center justify-center rounded-full bg-black/5 text-xs font-semibold text-black/65" x-text="user.name.slice(0, 1).toUpperCase()"></span>
                                    </template>
                                    <span x-text="user.name" class="truncate font-medium text-black/75"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            @else
                <div class="mb-3">
                    <label for="direct-search" class="sr-only">Pesquisar conversas diretas</label>
                    <input id="direct-search" wire:model.live.debounce.250ms="directSearch" type="text" class="w-full rounded-full border border-black/20 bg-white px-3 py-1.5 text-xs text-black/75 placeholder:text-black/40 focus:border-blue-300 focus:outline-none focus:ring-1 focus:ring-blue-300" placeholder="Pesquisar conversas...">
                </div>

                <div class="flex items-start gap-2 overflow-x-auto pb-1">
                    <button type="button" wire:click="togglePing" class="flex min-w-[3.2rem] flex-col items-center justify-start gap-1 text-center align-top" aria-label="Ping de utilizadores">
                        <div class="mx-auto inline-flex size-9 items-center justify-center rounded-full border border-black/15 bg-white text-black/70 shadow-sm transition hover:bg-blue-50 hover:text-blue-800 hover:border-blue-300">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <defs>
                                    <mask id="add-chat-mask">
                                    <rect x="0" y="0" width="24" height="24" fill="white" />
                                    <circle cx="19" cy="19" r="4.5" fill="black" /> 
                                    </mask>
                                </defs>

                                <g mask="url(#add-chat-mask)">
                                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                                </g>

                                <line x1="19" y1="16" x2="19" y2="22" stroke-width="1.5"></line>
                                <line x1="16" y1="19" x2="22" y2="19" stroke-width="1.5"></line>
                            </svg>
                        </div>
                        <p class="truncate text-[11px] leading-none text-black/55">Adicionar</p>
                    </button>

                    @foreach ($directConversations as $directConversation)
                    @php
                        $otherDirectParticipants = $directConversation->participants->where('id', '!=', auth()->id())->values();
                        $directParticipant = $otherDirectParticipants->first();
                        $isGroupDirectConversation = $otherDirectParticipants->count() > 1;

                        $groupPreviewParticipants = $otherDirectParticipants->take(2);
                        $groupPreviewLabel = $groupPreviewParticipants
                            ->map(fn ($participant) => $buildParticipantInitials($participant?->name))
                            ->implode('+');

                        if ($otherDirectParticipants->count() > 2) {
                            $groupPreviewLabel .= '+' . ($otherDirectParticipants->count() - 2);
                        }

                        $directParticipantName = $isGroupDirectConversation
                            ? ($groupPreviewLabel !== '' ? $groupPreviewLabel : 'Grupo')
                            : ($directParticipant?->name ?? $directConversation->name ?? 'Conversa direta');
                        $directParticipantPhoto = $directParticipant?->profile_photo_url;
                        $directParticipantStatusLabel = $this->userPresenceLabel($directParticipant);
                        $directParticipantStatusDotClass = $this->userPresenceDotClass($directParticipant);
                        $isDirectConversationUnread = $this->conversationHasUnreadNotification($directConversation);
                    @endphp

                    <button type="button" wire:click="selectConversation({{ $directConversation->id }})" class="flex min-w-[3.2rem] flex-col items-center justify-start gap-1 text-center ">
                        @if ($isGroupDirectConversation)
                            <div class="relative mx-auto h-9 w-11">
                                @foreach ($groupPreviewParticipants as $previewIndex => $previewParticipant)
                                    @php
                                        $previewLeftClass = $previewIndex === 0 ? 'left-0 z-10' : 'left-4 z-20';
                                    @endphp
                                    <span class="absolute top-0 inline-flex size-9 items-center justify-center overflow-hidden rounded-full border {{ $isDirectConversationUnread ? 'border-blue-800' : 'border-white' }} bg-[#ececec] text-[10px] font-semibold text-black/65 {{ $previewLeftClass }}">
                                        @if ($previewParticipant?->profile_photo_url)
                                            <img src="{{ $previewParticipant->profile_photo_url }}" alt="{{ $previewParticipant->name }}" class="size-full object-cover">
                                        @else
                                            {{ $buildParticipantInitials($previewParticipant?->name) }}
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <div class="relative mx-auto inline-flex size-9 items-center justify-center rounded-full border {{ $isDirectConversationUnread ? 'border-blue-800' : 'border-black/15' }} bg-white text-xs font-semibold {{ $isDirectConversationUnread ? 'text-blue-800' : 'text-black/70' }}">
                                <span class="inline-flex size-full items-center justify-center overflow-hidden rounded-full">
                                    @if ($directParticipantPhoto)
                                        <img src="{{ $directParticipantPhoto }}" alt="{{ $directParticipantName }}" class="size-full object-cover">
                                    @else
                                        {{ mb_strtoupper(mb_substr($directParticipantName, 0, 1)) }}
                                    @endif
                                </span>

                                <span class="absolute -bottom-0.5 -right-0.5 inline-flex size-2.5 rounded-full border border-white {{ $directParticipantStatusDotClass }}" title="{{ $directParticipantStatusLabel }}"></span>
                            </div>
                        @endif
                        <p class="truncate text-[11px] leading-none {{ $isDirectConversationUnread ? 'text-blue-800' : 'text-black/55' }}">{{ \Illuminate\Support\Str::limit($directParticipantName, 8) }}</p>
                        @if ($isDirectConversationUnread)
                            <span class="mx-auto mt-1 block size-1 rounded-full bg-blue-800"></span>
                        @endif
                    </button>
                @endforeach

                @if ($directConversations->isEmpty())
                    <div class="rounded-xl border border-dashed border-black/15 bg-white/50 px-4 py-6 text-center">
                        <p class="self-center px-3 text-xs text-black/45">
                            {{ trim($directSearch) !== '' ? 'Sem resultados para a pesquisa' : 'Sem mensagens diretas' }}
                        </p>
                    </div>
                @endif
            </div>
            @endif

            <div class="flex-1 overflow-hidden px-4 py-4">
                <div class="mb-8 border-t border-black/10"></div>
                <div class="mb-4 flex items-center gap-1 rounded-full border border-black/15 bg-white p-1">
                    <button
                        type="button"
                        wire:click="setRoomTab('my-rooms')"
                        class="flex-1 rounded-full px-3 py-1.5 text-xs font-semibold transition {{ $roomTab === 'my-rooms' ? 'bg-blue-800 text-white' : 'text-black/65 hover:bg-black/5' }}"
                    >
                        Minhas Salas
                    </button>
                    <button
                        type="button"
                        wire:click="setRoomTab('browse')"
                        class="flex-1 rounded-full px-3 py-1.5 text-xs font-semibold transition {{ $roomTab === 'browse' ? 'bg-blue-800 text-white' : 'text-black/65 hover:bg-black/5' }}"
                    >
                        Procurar
                    </button>
                </div>
                @if ($roomTab === 'my-rooms')
                    <div class="mb-3 flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-black/35">Salas</p>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-black/45">{{ $conversations->where('type', \App\Models\ChatConversation::TYPE_ROOM)->count() }}</span>
                            @if (auth()->user()?->isAdmin())
                                <button type="button" wire:click="toggleRoomForm" class="inline-flex size-7 items-center justify-center rounded-full border border-black/20 bg-white text-black/70 transition hover:bg-blue-50 hover:text-blue-800 hover:border-blue-300 focus:border-blue-300 focus:bg-blue-50" aria-label="Nova sala">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="max-h-48 space-y-2 overflow-y-auto">
                        @forelse ($conversations->where('type', \App\Models\ChatConversation::TYPE_ROOM) as $roomConversation)
                            @php
                                $roomHasNewMessages = $this->conversationHasUnreadNotification($roomConversation);
                            @endphp
                            <button type="button" wire:click="selectConversation({{ $roomConversation->id }})" class="flex w-full items-center gap-3 rounded-xl border px-3 py-2 text-left hover:bg-blue-50 hover:text-blue-800 hover:border-blue-300 transition {{ (int) $selectedConversationId === (int) $roomConversation->id ? 'border-blue-300 bg-blue-50' : ($roomHasNewMessages ? 'border-blue-800 bg-white' : 'border-black/10 bg-white hover:bg-black/5') }}">
                                @if ($roomConversation->avatar)
                                    <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm">
                                        {{ $roomConversation->avatar }}
                                    </span>
                                @else
                                    <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold text-blue-800">
                                        {{ mb_strtoupper(mb_substr($roomConversation->name ?: 'S', 0, 1)) }}
                                    </span>
                                @endif
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium {{ $roomHasNewMessages ? 'text-blue-800' : 'text-black/80' }}">{{ $roomConversation->name ?: 'Sala' }}</p>
                                    <p class="text-xs text-black/45">{{ $roomConversation->participants_count ?? $roomConversation->participants->count() }} membros</p>
                                </div>
                                @if ($roomHasNewMessages)
                                    <span class="size-2 shrink-0 rounded-full bg-blue-800"></span>
                                @endif
                            </button>
                        @empty
                            <div class="rounded-xl border border-dashed border-black/15 bg-white/50 px-4 py-6 text-center">
                                <p class="text-sm text-black/45">Não estás em nenhuma sala</p>
                                <button type="button" wire:click="setRoomTab('browse')" class="mt-2 text-xs font-semibold text-blue-800 hover:underline">Procurar salas</button>
                            </div>
                        @endforelse
                    </div>
                    @if ($showRoomForm && auth()->user()?->isAdmin())
                        <div class="mt-4 rounded-2xl border border-black/15 bg-white p-3">
                            <h2 class="text-sm font-semibold text-black/80">Criar sala</h2>
                            <div class="mt-2 space-y-2">
                                <input wire:model.defer="roomName" type="text" class="w-full rounded-xl border border-black/20 bg-white px-3 py-2 text-xs text-black/75 placeholder:text-black/40 focus:border-blue-300 focus:ring-blue-300" placeholder="Nome da sala">
                                @error('roomName') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                                <input wire:model.defer="roomAvatar" type="text" class="w-full rounded-xl border border-black/20 bg-white px-3 py-2 text-xs text-black/75 placeholder:text-black/40 focus:border-blue-300 focus:ring-blue-300" placeholder="Avatar (emoji ou símbolo)">
                                @error('roomAvatar') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                                <div class="max-h-24 space-y-1 overflow-y-auto rounded-xl border border-black/10 p-2">
                                    @foreach ($availableUsers as $user)
                                        <label class="flex items-center gap-2 rounded-lg px-2 py-1 hover:bg-black/5">
                                            <input wire:model.defer="roomParticipantIds" type="checkbox" value="{{ $user->id }}" class="rounded border-black/20 text-blue-800 focus:ring-blue-300">
                                            <span class="truncate text-xs text-black/70">{{ $user->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <button type="button" wire:click="createRoom" class="w-full rounded-full bg-blue-800 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-900">Criar sala</button>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="mb-3">
                        <p class="mb-2 text-xs font-semibold uppercase tracking-[0.16em] text-black/35">Procurar Salas</p>
                        <input
                            wire:model.live.debounce.300ms="roomSearch"
                            type="text"
                            class="w-full rounded-full border border-black/20 bg-white px-3 py-1.5 text-xs text-black/75 placeholder:text-black/40 focus:border-blue-300 focus:outline-none focus:ring-1 focus:ring-blue-300"
                            placeholder="Pesquisar por nome..."
                        >
                    </div>
                    <div class="max-h-64 space-y-2 overflow-y-auto">
                        @forelse ($browseRooms as $room)
                            <div class="flex items-center gap-3 rounded-xl border border-black/10 bg-white px-3 py-2 transition hover:border-black/20">
                                @if ($room->avatar)
                                    <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm">
                                        {{ $room->avatar }}
                                    </span>
                                @else
                                    <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold text-blue-800">
                                        {{ mb_strtoupper(mb_substr($room->name ?: 'S', 0, 1)) }}
                                    </span>
                                @endif
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-black/80">{{ $room->name ?: 'Sala' }}</p>
                                    <p class="text-xs text-black/45">{{ $room->participants_count }} membros</p>
                                </div>
                                <button
                                    type="button"
                                    wire:click="joinRoom({{ $room->id }})"
                                    class="shrink-0 rounded-full bg-blue-800 px-3 py-1 text-xs font-semibold text-white transition hover:bg-blue-900"
                                >
                                    Entrar
                                </button>
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-black/15 bg-white/50 px-4 py-6 text-center">
                                @if (trim($roomSearch) !== '')
                                    <p class="text-sm text-black/45">Nenhuma sala encontrada</p>
                                    <p class="mt-1 text-xs text-black/35">Tenta outro termo de pesquisa</p>
                                @else
                                    <p class="text-xs text-black/45">Não há salas disponíveis</p>
                                @endif
                            </div>
                        @endforelse
                    </div>
                @endif
            </div>
        </div>
    @endif
</aside>
