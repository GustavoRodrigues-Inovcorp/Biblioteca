@props(['livro'])
<div class="flex items-center justify-end gap-2">
    <a
        href="{{ route('admin.livros.edit', $livro) }}"
        class="inline-flex items-center rounded-md border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50"
    >
        Editar
    </a>
    <form
        method="POST"
        action="{{ route('admin.livros.destroy', $livro) }}"
        onsubmit="event.preventDefault(); event.stopPropagation(); showDeletePopup(this, 'delete-popup-{{ $livro->id }}');"
    >
        @csrf
        @method('DELETE')
        <button
            type="submit"
            onclick="event.stopPropagation();"
            class="inline-flex items-center rounded-md border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-50"
        >
            Eliminar
        </button>
    </form>
    <x-popup
        id="delete-popup-{{ $livro->id }}"
        color="red"
        title="Confirmação"
        :showCancel="true"
        okText="Confirmar"
        cancelText="Cancelar"
        message="Tens a certeza que queres eliminar este livro?"
    />
    <script>
        window.deleteForms = window.deleteForms || {};
        function showDeletePopup(form, popupId) {
            window.deleteForms[popupId] = form;
            document.getElementById(popupId).style.display = 'flex';
        }
        function closeDeletePopup(popupId) {
            document.getElementById(popupId).style.display = 'none';
            window.deleteForms[popupId] = null;
        }
        function confirmDeletePopup(popupId) {
            document.getElementById(popupId).style.display = 'none';
            if (window.deleteForms[popupId]) {
                window.deleteForms[popupId].onsubmit = null;
                window.deleteForms[popupId].submit();
            }
        }
        // Garantir que os botões do popup funcionam sempre
        document.addEventListener('DOMContentLoaded', function() {
            var popupId = 'delete-popup-{{ $livro->id }}';
            var popup = document.getElementById(popupId);
            if (popup) {
                var buttons = popup.querySelectorAll('button');
                if (buttons.length > 0) {
                    // Confirmar
                    buttons[0].onclick = function(e) {
                        e.stopPropagation();
                        confirmDeletePopup(popupId);
                    };
                    // Cancelar
                    if (buttons.length > 1) {
                        buttons[1].onclick = function(e) {
                            e.stopPropagation();
                            closeDeletePopup(popupId);
                        };
                    }
                }
                // Overlay
                var overlay = popup.querySelector('.absolute.inset-0');
                if (overlay) {
                    overlay.onclick = function(e) { e.stopPropagation(); };
                }
            }
        });
    </script>
</div>
