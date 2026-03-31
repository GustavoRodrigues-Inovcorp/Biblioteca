@component('mail::message')
    # Estado da sua Review

    Olá {{ $review->user->name }},

    @if ($estado === 'ativo')
        A sua review ao livro **{{ $review->livro->nome }}** foi **aprovada** e já está visível para outros utilizadores.
    @else
        A sua review ao livro **{{ $review->livro->nome }}** foi **recusada** pela administração.
        @if ($justificacao)
            **Justificação:**
            {{ $justificacao }}
        @endif
    @endif

    Se tiver dúvidas, contacte a administração.

    Obrigado,
    {{ config('app.name') }}
@endcomponent
