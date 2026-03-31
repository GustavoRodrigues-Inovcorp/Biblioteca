@component('mail::message')
    # Nova Review Submetida

    Um cidadão submeteu uma nova review.

    **Utilizador:** {{ $review->user->name }} ({{ $review->user->email }})
    **Livro:** {{ $review->livro->nome }}
    **Avaliação:** {{ $review->rating }} estrelas
    **Comentário:** {{ $review->comentario }}

    @component('mail::button', ['url' => route('livros.show', $review->livro_id)])
        Ver detalhe do livro
    @endcomponent
@endcomponent
