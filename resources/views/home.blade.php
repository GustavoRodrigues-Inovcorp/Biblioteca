<x-app-layout>
    <div class="home-page home-copy">
        <section class="home-hero">
            <div class="home-glow-left"></div>
            <div class="home-glow-right"></div>

            <div class="relative mx-auto max-w-7xl px-4 pb-16 pt-16 sm:px-6 lg:px-8 lg:pb-24 lg:pt-24">
                <div class="grid items-center gap-10 lg:grid-cols-2">
                    <div class="space-y-8 home-fade-up">
                        <p class="home-pill inline-flex w-fit items-center px-3 py-1 text-xs font-semibold uppercase tracking-wider">
                            Leitura para todos
                        </p>

                        <div class="space-y-5">
                            <h2 class="home-hero-title text-4xl font-semibold leading-tight tracking-tight sm:text-5xl">
                                Descobre mundos novos em cada página.
                            </h2>
                            <p class="max-w-2xl text-base leading-8 text-blue-100 sm:text-lg">
                                Pesquisa no catálogo, acompanha autores e requisita livros com um fluxo simples. A tua próxima leitura começa aqui.
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-3 pt-1">
                            <a href="{{ route('livros.index') }}" class="home-btn-primary inline-flex items-center px-6 py-3 text-sm">
                                Explorar livros
                            </a>

                            <a href="{{ route('autores.index') }}" class="home-btn-secondary inline-flex items-center px-6 py-3 text-sm">
                                Ver autores
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-gray-50 py-16 sm:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-6 md:grid-cols-3">
                    <a href="{{ route('livros.index') }}" class="home-card home-fade-up home-fade-up-delay-1 p-7">
                        <p class="text-xs font-bold uppercase tracking-wider text-blue-700">Catálogo</p>
                        <h3 class="mt-3 text-2xl font-semibold text-gray-900">Livros</h3>
                        <p class="mt-3 text-sm leading-7 text-gray-600">Filtra por título, área e disponibilidade para encontrares rapidamente o que precisas.</p>
                        <span class="mt-5 inline-flex text-sm font-semibold text-blue-700">Entrar no catálogo</span>
                    </a>

                    <a href="{{ route('autores.index') }}" class="home-card home-fade-up home-fade-up-delay-2 p-7">
                        <p class="text-xs font-bold uppercase tracking-wider text-blue-700">Comunidade</p>
                        <h3 class="mt-3 text-2xl font-semibold text-gray-900">Autores</h3>
                        <p class="mt-3 text-sm leading-7 text-gray-600">Explora os escritores presentes na plataforma e acompanha o seu percurso literário.</p>
                        <span class="mt-5 inline-flex text-sm font-semibold text-blue-700">Descobrir autores</span>
                    </a>

                    <a href="{{ route('editoras.index') }}" class="home-card home-fade-up home-fade-up-delay-3 p-7">
                        <p class="text-xs font-bold uppercase tracking-wider text-blue-700">Rede</p>
                        <h3 class="mt-3 text-2xl font-semibold text-gray-900">Editoras</h3>
                        <p class="mt-3 text-sm leading-7 text-gray-600">Consulta editoras parceiras e os títulos publicados em colaboração com a biblioteca.</p>
                        <span class="mt-5 inline-flex text-sm font-semibold text-blue-700">Ver editoras</span>
                    </a>
                </div>

                <div class="mt-14 rounded-3xl border border-gray-300 bg-white px-6 py-8 shadow-sm sm:px-8 sm:py-10">
                    <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="home-cta-title text-3xl font-bold text-gray-900">Pronto para começar a ler?</h3>
                            <p class="mt-3 max-w-2xl text-sm leading-7 text-gray-600">Cria uma conta para guardar preferências, requisitar livros e acompanhar as tuas leituras.</p>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            @guest
                                @if (Route::has('login'))
                                    <a href="{{ route('login') }}"
                                        class="inline-flex items-center rounded-full border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:border-blue-300 hover:text-blue-800">
                                        Entrar
                                    </a>
                                @endif

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}"
                                        class="inline-flex items-center whitespace-nowrap rounded-full bg-blue-800 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-900">
                                        Criar conta
                                    </a>
                                @endif
                            @endguest

                            @auth
                                <a href="{{ route('requisicoes.index') }}"
                                    class="inline-flex items-center whitespace-nowrap rounded-full bg-blue-800 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-900">
                                    As minhas requisições
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
