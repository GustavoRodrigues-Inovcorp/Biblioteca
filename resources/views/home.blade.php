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

            <div class="bg-white shadow-sm sm:rounded-lg p-5">
                <div class="flex flex-wrap items-center gap-3 justify-center">
                    @guest
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium text-gray-700 border border-gray-300 hover:bg-gray-50 transition">
                                Iniciar sessão
                            </a>
                        @endif

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium text-white bg-gray-900 hover:bg-black transition">
                                Criar conta
                            </a>
                        @endif
                    @endguest

                    @auth
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-secondary-button type="submit">
                                Sair
                            </x-secondary-button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
