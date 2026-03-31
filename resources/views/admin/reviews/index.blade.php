<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">
                    Reviews
                </h1>
                <p class="text-sm text-slate-600">
                    Gestão de reviews submetidos pelos utilizadores.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="mt-6 space-y-4">
        <livewire:admin.reviews-table />
    </div>
</x-admin-layout>
