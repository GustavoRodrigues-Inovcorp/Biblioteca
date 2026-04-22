<nav x-data="{ open: false }" class="sticky top-0 z-50 border-b border-slate-200 bg-white bg-opacity-90 backdrop-blur-sm">
    @php
        $isChatContext = request()->routeIs('chat.*');
        $totalCarrinho = collect(session('carrinho', []))->sum(fn ($qtd) => (int) $qtd);
        $isAutoresContext = request()->routeIs('autores.*');
        $isEditorasContext = request()->routeIs('editoras.*');
        $navSearchAction = $isAutoresContext ? route('autores.index') : ($isEditorasContext ? route('editoras.index') : route('livros.index'));
        $navSearchLabel = $isAutoresContext ? 'Pesquisar autores' : ($isEditorasContext ? 'Pesquisar editoras' : 'Pesquisar livros');
        $navSearchPlaceholder = $isAutoresContext ? 'Pesquisar autor...' : ($isEditorasContext ? 'Pesquisar editora...' : 'Pesquisar livros...');
    @endphp

    @if ($isChatContext)
        <div class="bg-blue-800 text-white shadow-sm">
            <div class="mx-auto flex h-14 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full text-white transition hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/40" aria-label="Voltar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="h-7 w-7" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m15 19-7-7 7-7" />
                    </svg>
                </a>

                <div class="flex flex-1 items-center justify-center px-3">
                    <span class="text-2xl font-bold uppercase">INOVBOOKS</span>
                </div>

                <div class="flex items-center gap-1 text-[11px] font-light">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                    <span class="whitespace-nowrap">Conversas</span>
                </div>
            </div>
        </div>
    @else

    <div class="border-b border-blue-900 bg-blue-800 text-white">
        <div class="mx-auto flex max-w-7xl items-center align-center justify-center px-4 py-0.5 sm:px-6 lg:px-8">
            <h1 class="flex items-center gap-1 font-medium tracking-wide text-white">
                <img style="height: 1.7rem; width: auto;" src="{{ asset('images/inovcorp.png') }}" alt="Inovcorp" />
                <span class="text-[9px] uppercase sm:text-[10px]">Inovcorp</span>
            </h1>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-16 items-center gap-4 py-3 md:gap-8 lg:gap-10">
            <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-1 text-slate-900">
                <x-application-mark class="block h-14 w-auto" />
                <span class="hidden text-xl font-bold font-serif uppercase tracking-wide text-slate-900 lg:block">INOVBOOKS</span>
            </a>

            <form method="GET" action="{{ $navSearchAction }}" class="hidden w-full max-w-2xl md:mx-4 md:block lg:mx-8">
                <label for="nav-search" class="sr-only">{{ $navSearchLabel }}</label>
                <div class="flex items-center overflow-hidden rounded-xl border border-slate-300 bg-slate-50 focus-within:border-blue-800 focus-within:ring-2 focus-within:ring-blue-200 transition">
                    <input id="nav-search" name="search" type="text" value="{{ request('search', request('q')) }}" placeholder="{{ $navSearchPlaceholder }}"
                        class="h-9 w-full border-none bg-transparent px-4 text-sm text-slate-700 placeholder:text-slate-500 focus:outline-none focus:ring-0">
                    <button type="submit" class="inline-flex h-9 items-center justify-center bg-blue-800 px-4 text-white hover:bg-blue-900 transition" aria-label="Pesquisar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                        </svg>
                        <span class="sr-only">Pesquisar</span>
                    </button>
                </div>
            </form>

            <div class="ml-auto hidden items-center gap-4 md:flex">
                @auth
                    <a href="{{ route('chat.index') }}" :active="request()->routeIs('chat.*')"
                        class="relative inline-flex items-center justify-center rounded-full border border-blue-200 p-2 text-slate-600 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-800 {{ request()->routeIs('carrinho.index') ? 'bg-blue-50 text-blue-800 ring-1 ring-blue-200' : '' }}"
                        title="Conversas">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                    </a>

                    @if (!auth()->user()->isAdmin())
                        <a href="{{ route('carrinho.index') }}"
                            class="relative inline-flex items-center justify-center rounded-full border border-blue-200 p-2 text-slate-600 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-800 {{ request()->routeIs('carrinho.index') ? 'bg-blue-50 text-blue-800 ring-1 ring-blue-200' : '' }}"
                            title="Carrinho">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.839l.383 1.437M7.5 14.25h9.75m-9.75 0L5.4 5.276M7.5 14.25 5.4 5.276m0 0L4.724 3.75M5.4 5.276h14.324a.75.75 0 0 1 .73.928l-1.086 5.43a.75.75 0 0 1-.73.572H7.5" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 19.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm9 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z" />
                            </svg>
                            @if ($totalCarrinho > 0)
                                <span class="absolute -right-1 -top-1 inline-flex min-w-5 items-center justify-center rounded-full bg-blue-800 px-1.5 py-0.5 text-[10px] font-semibold leading-none text-white">
                                    {{ $totalCarrinho }}
                                </span>
                            @endif
                        </a>
                    @endif

                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.logs.index') }}"
                            class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-blue-300 hover:text-blue-800 {{ request()->routeIs('admin.logs.*') ? 'border-blue-300 bg-blue-50 text-blue-800 ring-1 ring-blue-200' : '' }}"
                            title="Logs">
                            Logs
                        </a>
                    @endif

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button class="inline-flex items-center rounded-full border border-slate-200 bg-white p-0.5 text-sm transition hover:border-blue-200 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-200">
                                    <img class="size-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                </button>
                            @else
                                <button type="button" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>
                                    Perfil
                                </button>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <div class="block px-4 py-2 text-xs text-slate-500">Conta</div>

                            <x-dropdown-link href="{{ route('profile.show') }}">
                                Perfil
                            </x-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                    API Tokens
                                </x-dropdown-link>
                            @endif

                            @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                                <div class="border-t border-slate-200"></div>
                                <div class="block px-4 py-2 text-xs text-slate-500">Equipa</div>

                                <x-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                                    Configuracoes da equipa
                                </x-dropdown-link>

                                @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                    <x-dropdown-link href="{{ route('teams.create') }}">
                                        Criar nova equipa
                                    </x-dropdown-link>
                                @endcan

                                @if (Auth::user()->allTeams()->count() > 1)
                                    <div class="border-t border-slate-200"></div>
                                    <div class="block px-4 py-2 text-xs text-slate-500">Trocar de equipa</div>
                                    @foreach (Auth::user()->allTeams() as $team)
                                        <x-switchable-team :team="$team" />
                                    @endforeach
                                @endif
                            @endif

                            <div class="border-t border-slate-200"></div>

                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                    Terminar sessão
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endauth

                @guest
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="inline-flex items-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-blue-300 hover:text-blue-800">
                            Entrar
                        </a>
                    @endif
                @endguest
            </div>

            <div class="ml-auto flex items-center md:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-md p-2 text-slate-600 hover:bg-slate-100 hover:text-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-200 transition">
                    <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="hidden border-t border-slate-200 md:block">
            <div class="flex items-center justify-center gap-6 py-2.5 lg:gap-10">
                <x-nav-link href="{{ route('livros.index') }}" :active="request()->routeIs('livros.index')">
                    Livros
                </x-nav-link>
                <x-nav-link href="{{ route('autores.index') }}" :active="request()->routeIs('autores.index')">
                    Autores
                </x-nav-link>
                <x-nav-link href="{{ route('editoras.index') }}" :active="request()->routeIs('editoras.index')">
                    Editoras
                </x-nav-link>
                @auth
                    <x-nav-link href="{{ route('requisicoes.index') }}" :active="request()->routeIs('requisicoes.index')">
                        Requisições
                    </x-nav-link>
                @endauth
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-slate-200 bg-slate-50 md:hidden">
        <div class="space-y-2 px-4 pb-4 pt-3">
            <form method="GET" action="{{ $navSearchAction }}">
                <label for="nav-search-mobile" class="sr-only">{{ $navSearchLabel }}</label>
                <div class="flex items-center overflow-hidden rounded-lg border border-slate-300 bg-white focus-within:border-blue-800 focus-within:ring-2 focus-within:ring-blue-200 transition">
                    <input id="nav-search-mobile" name="search" type="text" value="{{ request('search', request('q')) }}" placeholder="{{ $navSearchPlaceholder }}"
                        class="h-10 w-full border-none bg-transparent px-3 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-0">
                    <button type="submit" class="inline-flex h-10 items-center justify-center bg-blue-800 px-3 text-white hover:bg-blue-900 transition" aria-label="Pesquisar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                        </svg>
                        <span class="sr-only">Pesquisar</span>
                    </button>
                </div>
            </form>

            <x-responsive-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">
                Inicio
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('livros.index') }}" :active="request()->routeIs('livros.index')">
                Livros
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('autores.index') }}" :active="request()->routeIs('autores.index')">
                Autores
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('editoras.index') }}" :active="request()->routeIs('editoras.index')">
                Editoras
            </x-responsive-nav-link>

            @auth
                <x-responsive-nav-link href="{{ route('chat.index') }}" :active="request()->routeIs('chat.*')">
                    Chat
                </x-responsive-nav-link>

                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    Perfil
                </x-responsive-nav-link>

                @if (!auth()->user()->isAdmin())
                    <x-responsive-nav-link href="{{ route('carrinho.index') }}" :active="request()->routeIs('carrinho.index')">
                        Carrinho @if ($totalCarrinho > 0)
                            ({{ $totalCarrinho }})
                        @endif
                    </x-responsive-nav-link>
                @endif

                @if (auth()->user()->isAdmin())
                    <x-responsive-nav-link href="{{ route('admin.logs.index') }}" :active="request()->routeIs('admin.logs.*')">
                        Logs
                    </x-responsive-nav-link>
                @endif

                <x-responsive-nav-link href="{{ route('requisicoes.index') }}" :active="request()->routeIs('requisicoes.index')">
                    Requisições
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                        Terminar sessão
                    </x-responsive-nav-link>
                </form>
            @endauth

            @guest
                @if (Route::has('login'))
                    <x-responsive-nav-link href="{{ route('login') }}" :active="request()->routeIs('login')">
                        Entrar
                    </x-responsive-nav-link>
                @endif

                @if (Route::has('register'))
                    <x-responsive-nav-link href="{{ route('register') }}" :active="request()->routeIs('register')">
                        Criar conta
                    </x-responsive-nav-link>
                @endif
            @endguest
        </div>
    </div>
    @endif
</nav>
