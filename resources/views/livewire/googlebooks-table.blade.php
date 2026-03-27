<div>
    {{-- Popup de confirmação de importação --}}
    @if ($mostrarPopupImportar && $livroParaImportar)
        <div id="confirmar-importar-popup" class="fixed top-0 left-0 w-full h-full flex items-center justify-center z-50" style="display:flex;">
            <div class="absolute inset-0 bg-black bg-opacity-60"></div>
            <div class="relative bg-white rounded-lg shadow-xl border border-green-400 p-6 max-w-md w-full animate-fade-in">
                <div class="flex items-center mb-4">
                    <svg class="w-8 h-8 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="text-lg font-semibold text-green-600">Confirmar importação</span>
                </div>
                <div class="text-green-700 mb-6 text-center break-words leading-relaxed max-w-[90%] mx-auto text-xs">
                    Tens a certeza que queres importar o livro <b>{{ $livroParaImportar['volumeInfo']['title'] ?? '' }}</b>?
                </div>
                <div class="flex justify-center gap-4">
                    <button wire:click="confirmarImportacao" class="bg-green-500 hover:bg-green-600 text-white font-medium px-4 py-2 rounded transition min-w-[100px] text-xs">Confirmar</button>
                    <button wire:click="cancelarImportacao" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-4 py-2 rounded transition min-w-[100px] text-xs">Cancelar</button>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.googlebooks.search') }}">
        @csrf
        <label for="q" class="text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Título, autor ou ISBN</label>
        <div class="flex gap-3 mb-4 items-center">
            <div class="flex-1">
                <div class="relative">
                    @php $hasValue = old('q', $q ?? '') !== ''; @endphp
                    @if(!$hasValue)
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.473 9.766l3.63 3.63a.75.75 0 1 0 1.06-1.06l-3.63-3.63A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd" />
                        </svg>
                    @else
                        <a href="{{ route('admin.googlebooks.index') }}" title="Limpar pesquisa"
                           class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500 transition flex items-center justify-center"
                           style="cursor:pointer; height: 2rem; width: 2rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="absolute mr-2 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                    <x-input
                        type="text"
                        placeholder="Pesquisar por ISBN, Título ou Autor..."
                        class="w-full rounded-xl border border-0.5 py-2 pl-10 pr-3 text-sm"
                        name="q"
                        id="q"
                        required
                        value="{{ old('q', $q ?? '') }}"
                    />
                </div>
            </div>
            <x-secondary-button
                type="submit"
                class="rounded-xl py-2 px-4"
            >
                Pesquisar
            </x-secondary-button>
        </div>
    </form>
    <div class="mt-10">
        <div class="overflow-x-auto rounded-xl shadow bg-white">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Capa</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Título</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Autores</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Editora</th>
                        <th class="px-4 py-3 ml-2 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @if(!isset($books))
                        <tr>
                            <td colspan="5" class="py-10 text-center text-xs text-slate-400">Nada foi pesquisado ainda.</td>
                        </tr>
                    @elseif(count($books) === 0)
                        <tr>
                            <td colspan="5" class="py-10 text-center text-xs text-slate-400">Nenhum livro encontrado.</td>
                        </tr>
                    @else
                        @foreach($books as $idx => $book)
                            @php
                                $info = $book['volumeInfo'] ?? [];
                                $isbn = collect($info['industryIdentifiers'] ?? [])->pluck('identifier')->first(fn($id) => strlen($id) === 13) ?? '';
                                $autores = isset($info['authors']) ? implode(', ', $info['authors']) : '';
                                $capa = $info['imageLinks']['thumbnail'] ?? null;
                            @endphp
                            <tr>
                                <td class="align-middle px-4 py-3">@if($capa)<img src="{{ $capa }}" alt="Capa" class="h-16 rounded shadow">@endif</td>
                                <td class="align-middle px-4 py-3">
                                    <div class="font-semibold">{{ $info['title'] ?? '' }}</div>
                                    @if($isbn)
                                        <div class="text-xs text-slate-500 mt-1">ISBN: {{ $isbn }}</div>
                                    @endif
                                </td>
                                <td class="align-middle px-4 py-3">
                                    @foreach(explode(',', $autores) as $autor)
                                        @if(trim($autor) !== '')
                                            <span class="inline-block bg-slate-100 rounded-full px-3 py-1 text-xs text-slate-700 mr-1 mb-1">{{ trim($autor) }}</span>
                                        @endif
                                    @endforeach
                                </td>
                                <td class="align-middle px-4 py-3 text-sm text-slate-500">{{ $info['publisher'] ?? '' }}</td>
                                <td class="align-middle px-4 py-3 text-sm text-slate-500">
                                    @if($isbn && isset($existingIsbns) && in_array($isbn, $existingIsbns))
                                        <span class="text-gray-400 text-xs ml-2">Importado</span>
                                    @else
                                        <button 
                                            type="button"
                                            class="inline-flex items-center rounded-md border border-emerald-200 px-3 py-1.5 text-xs font-medium text-emerald-700 transition hover:bg-emerald-50"
                                            wire:click="pedirConfirmacaoImportar({{ $idx }})"
                                        >
                                            Importar
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>