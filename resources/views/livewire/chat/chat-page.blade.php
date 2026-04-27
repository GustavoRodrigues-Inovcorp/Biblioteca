<div wire:poll.6s class="h-[calc(100dvh-var(--chat-top-offset,4rem))] overflow-hidden bg-gray-50 text-[#1e1e1e]">
    <div class="mx-auto flex h-full max-w-[1600px]">
        <aside class="hidden h-full w-20 flex-col border-r border-black/15 bg-gray-50 lg:flex">
            <div class="mt-auto flex justify-center px-2 pb-4">
                <img src="{{ asset('images/logo.png') }}" alt="Inovbooks" class="h-12 w-auto object-contain opacity-85">
            </div>
        </aside>

        <div class="grid h-full flex-1 grid-cols-1 lg:grid-cols-[minmax(0,1fr)_20rem]">
            @include('livewire.chat.partials.conversation-main')
            @include('livewire.chat.partials.conversation-sidebar')
        </div>
    </div>

    @includeIf('livewire.chat.partials.invitations_float')
</div>
