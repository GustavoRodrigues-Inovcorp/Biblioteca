@props(['livro', 'livrosRequisitados'])
<form method="POST" action="{{ route('requisicoes.store') }}" id="requisitarForm-{{ $livro->id }}" onsubmit="return checkRequisicoesLimite(event, {{ $livro->id }});">
    @csrf
    <input type="hidden" name="livro_id" value="{{ $livro->id }}">
    <button
        type="button"
        onclick="event.stopPropagation(); handleRequisitarClick({{ $livro->id }})"
        class="inline-flex items-center rounded-md border border-emerald-200 px-3 py-1.5 text-xs font-medium text-emerald-700 transition hover:bg-emerald-50"
        id="btn-requisitar-{{ $livro->id }}"
    >
        Requisitar
    </button>
</form>
<x-popup
    id="confirmar-requisicao-popup-{{ $livro->id }}"
    color="green"
    title="Confirmar requisição"
    :showCancel="true"
    okText="Confirmar"
    cancelText="Cancelar"
    message="Tens a certeza que queres requisitar este livro?"
    :onOk="'confirmarRequisicao(' . $livro->id . ')'"
    :onCancel="'closeConfirmarRequisicaoPopup(' . $livro->id . ')'"
    messageClass="text-xs"
/>
<x-popup
    id="limite-popup"
    color="yellow"
    title="Limite atingido"
    :showCancel="false"
    okText="OK"
    message="Já requisitaste 3 livros. Não podes requisitar mais até devolver algum."
    :onOk="'closeLimitePopup()'"
    messageClass="text-xs"
/>

<script>
    function checkRequisicoesLimite(event, livroId) {
        // Esta função só é chamada no submit do form via confirmarRequisicao
        // Não precisa de verificar o limite aqui, pois já foi verificado antes
        event.preventDefault();
        showConfirmarRequisicaoPopup(livroId);
        return false;
    }

    function handleRequisitarClick(livroId) {
        const livrosRequisitados = {{ $livrosRequisitados ?? 0 }};
        if (livrosRequisitados >= 3) {
            document.getElementById('limite-popup').style.display = 'flex';
            return;
        }
        showConfirmarRequisicaoPopup(livroId);
    }
    function showConfirmarRequisicaoPopup(livroId) {
        document.getElementById('confirmar-requisicao-popup-' + livroId).style.display = 'flex';
    }
    function closeConfirmarRequisicaoPopup(livroId) {
        document.getElementById('confirmar-requisicao-popup-' + livroId).style.display = 'none';
    }
    function confirmarRequisicao(livroId) {
        document.getElementById('confirmar-requisicao-popup-' + livroId).style.display = 'none';
        // Pequeno delay para garantir que o popup fecha antes do submit
        setTimeout(function() {
            var form = document.getElementById('requisitarForm-' + livroId);
            if (form) {
                form.submit();
            } else {
                alert('Erro: formulário não encontrado.');
            }
        }, 100);
    }

    @if ($errors->has('livro_id'))
        <div class="mt-2 text-xs text-red-600">{{ $errors->first('livro_id') }}</div>
    @endif

    function closeLimitePopup() {
        document.getElementById('limite-popup').style.display = 'none';
    }
</script>
