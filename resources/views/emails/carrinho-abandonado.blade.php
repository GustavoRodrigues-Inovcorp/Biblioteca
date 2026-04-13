<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Precisa de ajuda com a sua compra?</title>
</head>
<body>
    <p>Olá {{ $user->name }},</p>

    <p>
        Notámos que deixou alguns livros no carrinho há mais de 1 hora.
        Se estiver com alguma dúvida no processo de compra, estamos aqui para ajudar.
    </p>

    @if (!empty($itens))
        <p><strong>Livros no carrinho:</strong></p>
        <ul>
            @foreach ($itens as $item)
                <li>{{ $item['titulo'] }} (x{{ $item['quantidade'] }})</li>
            @endforeach
        </ul>
    @endif

    <p>
        Pode retomar a sua compra em qualquer momento na área de carrinho.
    </p>

    <p>Obrigado,<br>Equipa Biblioteca</p>
</body>
</html>
