<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Requisição de Livro</title>
</head>
<body style="background:#f6f8fa;padding:0;margin:0;font-family:Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f6f8fa;padding:30px 0;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;background:#fff;border-radius:8px;box-shadow:0 2px 8px #0001;padding:32px 24px;">
                    <tr>
                        <td align="center" style="padding-bottom:24px;">
                            <h2 style="margin:0;font-size:1.6rem;color:#1a202c;font-weight:700;">Confirmação de Requisição de Livro</h2>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom:12px;font-size:1rem;color:#222;">Olá <strong>{{ $user->name }}</strong>,</td>
                    </tr>
                    <tr>
                        <td style="padding-bottom:18px;font-size:1rem;color:#222;">A sua requisição foi registada com sucesso. Aqui estão os detalhes:</td>
                    </tr>
                    <tr>
                        <td>
                            <table cellpadding="0" cellspacing="0" width="100%" style="background:#f9fafb;border-radius:6px;padding:16px 12px;margin-bottom:18px;">
                                <tr>
                                    <td style="padding:8px 0;"><strong>Número da Requisição:</strong> {{ $requisicao->numero }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;"><strong>Livro:</strong> {{ $livro->nome }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;"><strong>ISBN:</strong> {{ $livro->isbn }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;"><strong>Editora:</strong> {{ $livro->editora->nome ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;"><strong>Requisitado em:</strong> {{ \Carbon\Carbon::parse($requisicao->requisitado_em)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;"><strong>Devolução prevista:</strong> {{ \Carbon\Carbon::parse($requisicao->fim_previsto_em)->format('d/m/Y') }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @if($livro->imagem_capa)
                    <tr>
                        <td align="center" style="padding-bottom:18px;">
                            <img src="{{ asset('storage/' . $livro->imagem_capa) }}" alt="Capa do Livro" style="max-width:120px;border-radius:6px;border:1px solid #e2e8f0;box-shadow:0 1px 4px #0001;" />
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding-top:10px;font-size:1rem;color:#222;">Obrigado por utilizar a nossa biblioteca!</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
