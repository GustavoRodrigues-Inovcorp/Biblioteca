<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-violet-50 text-violet-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 20h16" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 20V7l7-3 7 3v13" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20v-4h6v4" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01" />
                </svg>
            </span>
            {{ __('Biblioteca') }}
        </h1>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @guest
            {{-- Utilizador não autenticado --}}
            <section class="bg-white shadow-sm sm:rounded-lg p-8 text-center">
                <div class="mx-auto max-w-xl">
                    <span class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-violet-50 text-violet-600 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 20h16" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 20V7l7-3 7 3v13" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 20v-4h6v4" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01" />
                        </svg>
                    </span>

                    <h2 class="text-3xl font-bold text-gray-900">Biblioteca</h2>
                    <p class="mt-3 text-gray-600 text-lg">
                        Gestão de livros, autores e editoras.
                    </p>

                    <div class="mt-8 flex justify-center gap-3">
                        <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 rounded-md text-sm font-semibold text-white bg-gray-900 hover:bg-black transition">
                            Iniciar Sessão
                        </a>
                        @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 rounded-md text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
                            Criar Conta
                        </a>
                        @endif
                    </div>
                </div>
            </section>

            {{-- Destaques públicos --}}
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="bg-white shadow-sm sm:rounded-lg p-5 flex items-center gap-4">
                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5A2.5 2.5 0 0 1 5.5 5H20v14H6.5A3.5 3.5 0 0 0 3 22V7.5Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M7 8h9M7 12h9" /></svg>
                    </span>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalLivros }}</p>
                        <p class="text-sm text-gray-500">Livros</p>
                    </div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-5 flex items-center gap-4">
                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5a7.5 7.5 0 0 1 15 0" /></svg>
                    </span>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalAutores }}</p>
                        <p class="text-sm text-gray-500">Autores</p>
                    </div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-5 flex items-center gap-4">
                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 20h18" /><path stroke-linecap="round" stroke-linejoin="round" d="M5 20V8l7-4 7 4v12" /><path stroke-linecap="round" stroke-linejoin="round" d="M9 20v-5h6v5" /></svg>
                    </span>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalEditoras }}</p>
                        <p class="text-sm text-gray-500">Editoras</p>
                    </div>
                </div>
            </div>
            @endguest

            {{-- Utilizador autenticado --}}
            @auth
            <section class="bg-white shadow-sm sm:rounded-lg p-6">
                <h2 class="text-2xl font-semibold text-gray-900">Bem-vindo, {{ Auth::user()->name }}</h2>
                <p class="mt-2 text-gray-600">
                    Aqui tens um resumo rápido do estado atual da biblioteca.
                </p>

                <div class="mt-6 grid gap-4 sm:grid-cols-3">
                    <a href="{{ route('livros') }}" class="rounded-lg border border-gray-200 p-4 hover:border-gray-300 hover:shadow-sm transition">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5A2.5 2.5 0 0 1 5.5 5H20v14H6.5A3.5 3.5 0 0 0 3 22V7.5Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h9M7 12h9" />
                            </svg>
                        </span>
                        <p class="mt-3 text-sm text-gray-500">Livros</p>
                        <p class="mt-1 text-3xl font-bold text-gray-900">{{ $totalLivros }}</p>
                        <p class="mt-2 text-sm text-blue-600">Ver livros</p>
                    </a>

                    <a href="{{ route('autores') }}" class="rounded-lg border border-gray-200 p-4 hover:border-gray-300 hover:shadow-sm transition">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5a7.5 7.5 0 0 1 15 0" /></svg>
                        </span>
                        <p class="mt-3 text-sm text-gray-500">Autores</p>
                        <p class="mt-1 text-3xl font-bold text-gray-900">{{ $totalAutores }}</p>
                        <p class="mt-2 text-sm text-emerald-600">Gerir autores</p>
                    </a>

                    <a href="{{ route('editoras') }}" class="rounded-lg border border-gray-200 p-4 hover:border-gray-300 hover:shadow-sm transition">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 20h18" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 20V8l7-4 7 4v12" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20v-5h6v5" />
                            </svg>
                        </span>
                        <p class="mt-3 text-sm text-gray-500">Editoras</p>
                        <p class="mt-1 text-3xl font-bold text-gray-900">{{ $totalEditoras }}</p>
                        <p class="mt-2 text-sm text-amber-600">Gerir editoras</p>
                    </a>
                </div>
            </section>
            @endauth

        </div>
    </div>
</x-app-layout>
