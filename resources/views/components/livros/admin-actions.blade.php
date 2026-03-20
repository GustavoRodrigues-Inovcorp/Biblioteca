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
        onsubmit="event.preventDefault(); showDeletePopup(this);"
    >
        @csrf
        @method('DELETE')
        <button
            type="submit"
            class="inline-flex items-center rounded-md border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-50"
        >
            Eliminar
        </button>
    </form>
    <x-popup
        id="delete-popup"
        color="red"
        title="Confirmação"
        :showCancel="true"
        okText="Confirmar"
        cancelText="Cancelar"
        message="Tens a certeza que queres eliminar este livro?"
        :onOk="'confirmDeletePopup()'"
        :onCancel="'closeDeletePopup()'"
    />
    <script>
        let deleteForm = null;
        function showDeletePopup(form) {
            deleteForm = form;
            document.getElementById('delete-popup').style.display = 'flex';
        }
        function closeDeletePopup() {
            document.getElementById('delete-popup').style.display = 'none';
            deleteForm = null;
        }
        function confirmDeletePopup() {
            document.getElementById('delete-popup').style.display = 'none';
            if (deleteForm) deleteForm.submit();
        }
    </script>
</div>
