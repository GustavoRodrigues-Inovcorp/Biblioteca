<x-admin-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Criar Utilizador Admin</h1>
            <p class="text-sm text-slate-600">Apenas utilizadores Admin podem criar novos Admins.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-2xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.admin-users.store') }}" class="space-y-5">
            @csrf

            <div>
                <x-label for="name" value="Nome" />
                <x-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                <x-input-error for="name" class="mt-2" />
            </div>

            <div>
                <x-label for="email" value="Email" />
                <x-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                <x-input-error for="email" class="mt-2" />
            </div>

            <div>
                <x-label for="password" value="Password" />
                <x-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                <x-input-error for="password" class="mt-2" />
            </div>

            <div>
                <x-label for="password_confirmation" value="Confirmar Password" />
                <x-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.admin-users.index') }}" class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-gray-700 transition hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-white transition hover:bg-emerald-700">
                    Criar Admin
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
