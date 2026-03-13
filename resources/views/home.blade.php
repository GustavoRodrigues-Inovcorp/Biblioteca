<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            {{ __('Biblioteca') }}
        </h1>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Destaques públicos --}}
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="bg-white shadow-sm sm:rounded-lg p-5 flex items-center gap-4">
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalLivros }}</p>
                        <p class="text-sm text-gray-500">Livros</p>
                    </div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-5 flex items-center gap-4">
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalAutores }}</p>
                        <p class="text-sm text-gray-500">Autores</p>
                    </div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-5 flex items-center gap-4">
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalEditoras }}</p>
                        <p class="text-sm text-gray-500">Editoras</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
