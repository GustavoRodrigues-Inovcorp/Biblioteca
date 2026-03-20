<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Lembrete de Entrega de Livro</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8fafc; color: #222; }
        .container { background: #fff; padding: 32px; border-radius: 8px; max-width: 500px; margin: 32px auto; box-shadow: 0 2px 8px #e2e8f0; }
        .header { font-size: 1.3em; font-weight: bold; margin-bottom: 16px; color: #2563eb; }
        .livro-capa { max-width: 120px; border-radius: 4px; margin-bottom: 16px; }
        .info { margin-bottom: 12px; }
        .footer { margin-top: 24px; font-size: 0.95em; color: #64748b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">Lembrete: Entrega do Livro Amanhã</div>
        <div class="info">
            Olá {{ $user->name }},<br>
            Este é um lembrete de que o prazo de entrega do livro abaixo termina amanhã ({{ \Carbon\Carbon::parse($requisicao->data_entrega)->format('d/m/Y') }}):
        </div>
        <div class="info">
            <strong>Título:</strong> {{ $livro->titulo ?? $livro->nome }}<br>
            <strong>Autores:</strong>
            @if($livro->autores && count($livro->autores))
                {{ $livro->autores->pluck('nome')->join(', ') }}
            @else
                -
            @endif<br>
            <strong>Editora:</strong> {{ $livro->editora->nome ?? '-' }}<br>
        </div>
        @if($livro->capa)
            <img class="livro-capa" src="{{ asset('storage/' . $livro->capa) }}" alt="Capa do Livro">
        @endif
        <div class="footer">
            Por favor, devolva o livro na biblioteca até à data limite.<br>
            Obrigado por utilizar a nossa biblioteca!
        </div>
    </div>
</body>
</html>
