@if (auth()->user()?->isAdmin())
    <x-admin-layout>
        <div class="-mx-4 -mb-8 sm:-mx-6 lg:-mx-8" style="--chat-top-offset: 5rem;">
            <livewire:chat.chat-page />
        </div>
    </x-admin-layout>
@else
    <x-app-layout>
        <div class="bg-slate-950 text-slate-100" style="--chat-top-offset: 4rem;">
            <livewire:chat.chat-page />
        </div>
    </x-app-layout>
@endif