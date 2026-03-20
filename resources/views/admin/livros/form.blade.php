@php
    $livro = $livro ?? null;
    $editoras = $editoras ?? collect();
    $autores = $autores ?? collect();
    $method = $method ?? 'POST';
    $submitLabel = $submitLabel ?? 'Guardar';
    $editoraInput = old('editora_input', $livro?->editora?->nome ?? '');
    $editorasSugestoes = $editoras->pluck('nome')->values();
    $autoresInput = old('autores_input', $livro?->autores?->pluck('nome')->implode(', ') ?? '');
    $autoresSugestoes = $autores->pluck('nome')->values();
    $imagemCapaAtual = $livro?->imagem_capa;
@endphp

<form method="POST" action="{{ $submitRoute }}" enctype="multipart/form-data" class="space-y-5">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    {{-- Campos base reutilizaveis do livro --}}
    <div class="grid gap-5 sm:grid-cols-2">
        <x-admin.livros.input-field
            id="isbn"
            name="isbn"
            label="ISBN (13 dígitos)"
            :value="old('isbn', $livro?->isbn)"
            required
            maxlength="13"
        />

        <x-admin.livros.input-field
            id="nome"
            name="nome"
            label="Título"
            :value="old('nome', $livro?->nome)"
            required
        />

        <x-admin.livros.input-field
            id="editora_input"
            name="editora_input"
            label="Editora"
            :value="$editoraInput"
            autocomplete="off"
        >
            <div id="editoras_sugestoes" class="mt-1 hidden max-h-48 overflow-auto rounded-md border border-gray-200 bg-white shadow-sm"></div>
        </x-admin.livros.input-field>

        <x-admin.livros.input-field
            id="preco"
            name="preco"
            label="Preço"
            type="number"
            :value="old('preco', $livro?->preco)"
            step="0.01"
            min="0"
            required
        />
    </div>

    <x-admin.livros.input-field
        id="autores_input"
        name="autores_input"
        label="Autores"
        :value="$autoresInput"
        autocomplete="off"
    >
            {{-- Campo de autores com sugestoes e ajuda contextual --}}
        <div id="autores_sugestoes" class="mt-1 hidden max-h-48 overflow-auto rounded-md border border-gray-200 bg-white shadow-sm"></div>
        <p class="mt-1 text-xs text-gray-500">Escreve um ou vários autores separados por vírgula.</p>
    </x-admin.livros.input-field>

    <div class="grid gap-5 sm:grid-cols-2">
        <div id="editora_logotipo_wrapper" class="{{ $errors->has('editora_logotipo') ? '' : 'hidden' }}">
            <label for="editora_logotipo" class="block text-sm font-medium text-gray-700">Logótipo da editora (se for nova)</label>
            <input id="editora_logotipo" name="editora_logotipo" type="file" accept="image/*" class="mt-1 w-full rounded-md border-gray-300 text-sm file:mr-4 file:rounded-md file:border-0 file:bg-gray-800 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-white file:uppercase hover:file:bg-gray-700 focus:border-gray-500 focus:ring-gray-500 transition">
            <p class="mt-1 text-xs text-gray-500">Obrigatório quando a editora escrita não existe ainda.</p>
            @error('editora_logotipo')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div id="autor_foto_wrapper" class="{{ $errors->has('autor_foto') ? '' : 'hidden' }}">
            <label for="autor_foto" class="block text-sm font-medium text-gray-700">Foto do(s) autor(es) novo(s)</label>
            <input id="autor_foto" name="autor_foto" type="file" accept="image/*" class="mt-1 w-full rounded-md border-gray-300 text-sm file:mr-4 file:rounded-md file:border-0 file:bg-gray-800 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-white file:uppercase hover:file:bg-gray-700 focus:border-gray-500 focus:ring-gray-500 transition">
            <p class="mt-1 text-xs text-gray-500">Obrigatório quando escreveres autor(es) que ainda não existem.</p>
            @error('autor_foto')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

        {{-- Script de autocomplete isolado do markup --}}
    <x-admin.livros.autocomplete-script :editoras-sugestoes="$editorasSugestoes" :autores-sugestoes="$autoresSugestoes" />

        {{-- Upload e preview da capa atual --}}
    <div>
        <label for="imagem_capa" class="block text-sm font-medium text-gray-700">Imagem da capa</label>
        <input id="imagem_capa" name="imagem_capa" type="file" accept="image/*" class="mt-1 w-full rounded-md border-gray-300 text-sm file:mr-4 file:rounded-md file:border-0 file:bg-gray-800 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-white file:uppercase hover:file:bg-gray-700 focus:border-gray-500 focus:ring-gray-500 transition">
        <p class="mt-1 text-xs text-gray-500">Formatos permitidos: JPG, PNG, WEBP. Máximo: 2MB.</p>
        @if ($imagemCapaAtual)
            <p class="mt-2 text-xs text-gray-500">Capa atual:</p>
            <img
                src="{{ str_starts_with($imagemCapaAtual, 'http') ? $imagemCapaAtual : asset('storage/' . $imagemCapaAtual) }}"
                alt="Capa atual"
                class="mt-1 h-24 w-16 rounded border border-gray-200 object-cover"
            >
        @endif
        @error('imagem_capa')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

        {{-- Campo de texto longo do livro --}}
    <div>
        <label for="bibliografia" class="block text-sm font-medium text-gray-700">Bibliografia</label>
        <textarea id="bibliografia" name="bibliografia" rows="5" class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500">{{ old('bibliografia', $livro?->bibliografia) }}</textarea>
        @error('bibliografia')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

        {{-- Acoes do formulario --}}
    <div class="flex items-center gap-3">
        <x-button type="submit">
            {{ $submitLabel }}
        </x-button>
        <a href="{{ route('admin.livros') }}" class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-gray-700 hover:bg-gray-50 transition">
            Cancelar
        </a>
    </div>
</form>