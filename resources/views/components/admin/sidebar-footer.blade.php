<div class="absolute bottom-0 inset-x-0 p-4 border-t border-gray-800/70">
    <p class="text-xs text-gray-400">Sessão iniciada como</p>
    <div class="mt-2 flex items-center gap-3">
        <img class="size-9 rounded-full object-cover" src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" />
        <div class="min-w-0">
            <p class="text-sm font-semibold truncate">{{ auth()->user()?->name }}</p>
            <p class="text-xs text-gray-400 truncate">{{ auth()->user()?->email }}</p>
        </div>
        <a href="{{ route('admin.perfil') }}" class="ml-auto inline-flex items-center rounded-md bg-gray-800 px-2.5 py-1.5 text-xs font-medium text-gray-100 hover:bg-gray-700 transition">
            Editar
        </a>
    </div>

    <div class="mt-3">
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-rose-600 px-3 py-2 text-xs font-medium text-white hover:bg-rose-500 transition">Terminar sessão</button>
        </form>
    </div>
</div>
