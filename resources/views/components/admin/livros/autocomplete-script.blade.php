@props([
    'editorasSugestoes' => [],
    'autoresSugestoes' => [],
])

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Normaliza texto para pesquisa sem acentos e com espacos consistentes.
        const normalize = function (value) {
            return value
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/\s+/g, ' ')
                .trim();
        };

        // Liga o comportamento de sugestoes a um input especifico.
        const setupAutocomplete = function (config) {
            const input = document.getElementById(config.inputId);
            const box = document.getElementById(config.boxId);
            const items = config.items;

            if (!input || !box || !Array.isArray(items)) {
                return;
            }

            const hideSuggestions = function () {
                box.classList.add('hidden');
                box.innerHTML = '';
            };

            const getCurrentToken = function () {
                if (config.multiple) {
                    const parts = input.value.split(',');
                    return parts[parts.length - 1].trim();
                }

                return input.value.trim();
            };

            const applySuggestion = function (selectedName) {
                if (config.multiple) {
                    const parts = input.value.split(',');
                    parts[parts.length - 1] = ' ' + selectedName;
                    input.value = parts
                        .join(',')
                        .replace(/^\s+/, '')
                        .replace(/\s+,/g, ',')
                        .replace(/,\s*/g, ', ')
                        .trim();

                    if (input.value !== '' && !input.value.endsWith(',')) {
                        input.value += ', ';
                    }
                } else {
                    input.value = selectedName;
                }

                hideSuggestions();
                input.focus();
            };

            // Mostra no maximo 8 correspondencias enquanto o utilizador escreve.
            input.addEventListener('input', function () {
                const token = getCurrentToken();
                const normalizedToken = normalize(token);

                if (normalizedToken.length < 2) {
                    hideSuggestions();
                    return;
                }

                const matches = items
                    .filter(function (nome) {
                        return normalize(String(nome)).includes(normalizedToken);
                    })
                    .slice(0, 8);

                if (matches.length === 0) {
                    hideSuggestions();
                    return;
                }

                box.innerHTML = '';

                matches.forEach(function (nome) {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'block w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50';
                    button.textContent = nome;
                    button.addEventListener('mousedown', function (event) {
                        event.preventDefault();
                        applySuggestion(String(nome));
                    });

                    box.appendChild(button);
                });

                box.classList.remove('hidden');
            });

            input.addEventListener('blur', function () {
                window.setTimeout(hideSuggestions, 150);
            });
        };

        const hasExactMatch = function (items, value) {
            const normalizedValue = normalize(String(value || ''));

            if (normalizedValue === '') {
                return false;
            }

            return items.some(function (item) {
                return normalize(String(item)) === normalizedValue;
            });
        };

        const parseAuthorTokens = function (rawValue) {
            return String(rawValue || '')
                .split(/[\r\n,;]+/)
                .map(function (part) {
                    return part.trim();
                })
                .filter(function (part) {
                    return normalize(part) !== '';
                });
        };

        const setupConditionalMediaFields = function () {
            const editoraInput = document.getElementById('editora_input');
            const autoresInput = document.getElementById('autores_input');
            const editoraWrapper = document.getElementById('editora_logotipo_wrapper');
            const autorWrapper = document.getElementById('autor_foto_wrapper');

            if (!editoraInput || !autoresInput || !editoraWrapper || !autorWrapper) {
                return;
            }

            const editoras = @json($editorasSugestoes);
            const autores = @json($autoresSugestoes);

            const refreshVisibility = function () {
                const editoraValue = editoraInput.value.trim();
                const isNewEditora = editoraValue !== '' && !hasExactMatch(editoras, editoraValue);

                const authorTokens = parseAuthorTokens(autoresInput.value);
                const hasAnyNewAuthor = authorTokens.some(function (token) {
                    return !hasExactMatch(autores, token);
                });

                editoraWrapper.classList.toggle('hidden', !isNewEditora);
                autorWrapper.classList.toggle('hidden', !hasAnyNewAuthor);
            };

            ['input', 'change', 'blur'].forEach(function (eventName) {
                editoraInput.addEventListener(eventName, refreshVisibility);
                autoresInput.addEventListener(eventName, refreshVisibility);
            });

            refreshVisibility();
        };

        // Configuracao para editora (valor unico) e autores (lista separada por virgulas).
        setupAutocomplete({
            inputId: 'editora_input',
            boxId: 'editoras_sugestoes',
            items: @json($editorasSugestoes),
            multiple: false,
        });

        setupAutocomplete({
            inputId: 'autores_input',
            boxId: 'autores_sugestoes',
            items: @json($autoresSugestoes),
            multiple: true,
        });

        setupConditionalMediaFields();
    });
</script>
