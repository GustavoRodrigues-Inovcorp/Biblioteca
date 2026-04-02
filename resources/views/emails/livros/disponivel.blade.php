@component('mail::message')
# O livro "{{ $livro->nome }}" está disponível!

O livro que pretende requisitar já se encontra disponível na biblioteca.

@component('mail::button', ['url' => route('livros.show', $livro->id)])
Ver livro
@endcomponent

Obrigado,<br>
{{ config('app.name') }}
@endcomponent