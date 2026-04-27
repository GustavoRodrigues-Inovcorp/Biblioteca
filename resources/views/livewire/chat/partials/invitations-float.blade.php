@if (! auth()->user()?->isAdmin())
    <div
        x-data="{ openInvitations: false }"
        class="fixed bottom-6 right-6 z-50"
    >
        <button
            type="button"
            @click="openInvitations = !openInvitations"
            class="relative inline-flex size-11 items-center justify-center rounded-full border border-black/20 bg-white text-black/70 shadow-lg transition hover:bg-blue-50 hover:text-blue-800 hover:border-blue-200"
            aria-label="Convites de salas"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
            </svg>

            @if ($pendingInvitations->isNotEmpty())
                <span class="absolute -right-1 -top-1 flex size-5 items-center justify-center rounded-full bg-blue-800 text-[10px] font-bold text-white ring-2 ring-white">
                    {{ $pendingInvitations->count() > 9 ? '9+' : $pendingInvitations->count() }}
                </span>
            @endif
        </button>

        <div
            x-cloak
            x-show="openInvitations"
            @click.outside="openInvitations = false"
            class="absolute bottom-14 right-0 z-50 w-80 overflow-hidden rounded-2xl border border-black/10 bg-white shadow-xl"
        >
            <div class="border-b border-black/10 px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-black/45">Convites</p>
            </div>

            <div class="max-h-96 overflow-y-auto">
                @forelse ($pendingInvitations as $invitation)
                    <div class="flex flex-col gap-3 border-b border-black/5 px-4 py-3 last:border-0">
                        <div class="flex items-center gap-3">
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-800">
                                {{ $invitation->conversation->avatar ?: mb_strtoupper(mb_substr($invitation->conversation->name ?: 'S', 0, 1)) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-black/80">{{ $invitation->conversation->name ?: 'Sala' }}</p>
                                <div class="mt-0.5 flex items-center gap-1.5">
                                    <span class="inline-flex size-4 items-center justify-center overflow-hidden rounded-full bg-[#ececec] text-[9px] font-semibold text-black/60">
                                        @if ($invitation->invitedBy?->profile_photo_url)
                                            <img src="{{ $invitation->invitedBy->profile_photo_url }}" alt="{{ $invitation->invitedBy->name }}" class="size-full object-cover">
                                        @else
                                            {{ mb_strtoupper(mb_substr($invitation->invitedBy?->name ?? 'U', 0, 1)) }}
                                        @endif
                                    </span>
                                    <p class="truncate text-xs text-black/45">Convidado por {{ $invitation->invitedBy?->name ?? 'Utilizador' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                wire:click="acceptInvitation({{ $invitation->id }})"
                                @click="openInvitations = false"
                                class="flex-1 rounded-full bg-blue-800 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-blue-900"
                            >
                                Aceitar
                            </button>
                            <button
                                type="button"
                                wire:click="declineInvitation({{ $invitation->id }})"
                                class="flex-1 rounded-full border border-black/10 bg-white px-3 py-1.5 text-xs font-semibold text-black/65 transition hover:bg-blue-50 hover:text-blue-800 hover:border-blue-200"
                            >
                                Recusar
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-8 text-center">
                        <p class="text-xs text-black/45">Sem convites pendentes</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endif
