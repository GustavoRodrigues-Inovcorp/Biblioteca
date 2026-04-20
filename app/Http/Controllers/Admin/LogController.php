<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LogController extends Controller
{
    private const MODULE_LABELS = [
        'Requisicao' => 'Requisição',
        'Livro' => 'Livro',
        'Review' => 'Review',
        'User' => 'Utilizador',
        'Encomenda' => 'Encomenda',
        'Autor' => 'Autor',
        'Editora' => 'Editora',
    ];

    private const FIELD_LABELS = [
        'numero' => 'Número',
        'user_id' => 'Utilizador',
        'livro_id' => 'Livro',
        'cart_items_snapshot' => 'Itens do carrinho',
        'cart_updated_at' => 'Carrinho atualizado em',
        'payment_status' => 'Estado de pagamento',
        'paid_at' => 'Pago em',
        'remember_token' => 'Token de sessão',
        'requisitado_em' => 'Requisitado em',
        'fim_previsto_em' => 'Fim previsto em',
        'devolvido_em' => 'Devolvido em',
        'pedido_devolucao_em' => 'Pedido de devolução em',
        'estado_devolucao' => 'Estado devolução',
        'nome' => 'Nome',
        'email' => 'Email',
        'role' => 'Perfil',
        'isbn' => 'ISBN',
        'preco' => 'Preço',
    ];

    private const ACTION_LABELS = [
        'created' => 'Criado',
        'updated' => 'Atualizado',
        'deleted' => 'Apagado',
        'restored' => 'Restaurado',
        'create' => 'Criado',
        'update' => 'Atualizado',
        'delete' => 'Apagado',
    ];

    private const SENSITIVE_FIELD_PATTERNS = [
        'password',
        'token',
        'secret',
        'api_key',
        'apikey',
        'authorization',
        'cookie',
        'stripe_',
    ];

    public function index(Request $request): View
    {
        $modulo = trim((string) $request->query('modulo', ''));
        $utilizador = trim((string) $request->query('utilizador', ''));
        $objetoId = trim((string) $request->query('objeto_id', ''));
        $dataInicio = trim((string) $request->query('data_inicio', ''));
        $dataFim = trim((string) $request->query('data_fim', ''));
        $search = trim((string) $request->query('search', ''));
        $searchTooShort = $search !== '' && mb_strlen($search) < 3;

        $logsQuery = ActivityLog::query()
            ->with('user:id,name,email')
            ->orderByDesc('created_at');

        if ($modulo !== '') {
            $logsQuery->where('modulo', $modulo);
        }

        if ($utilizador !== '') {
            $logsQuery->whereHas('user', function ($userQuery) use ($utilizador): void {
                $userQuery
                    ->where('name', 'like', "%{$utilizador}%")
                    ->orWhere('email', 'like', "%{$utilizador}%");
            });
        }

        if ($objetoId !== '') {
            $logsQuery->where('objeto_id', $objetoId);
        }

        if ($dataInicio !== '') {
            try {
                $logsQuery->where('created_at', '>=', Carbon::parse($dataInicio)->startOfDay());
            } catch (\Throwable) {
                // Ignora datas inválidas para manter o filtro resiliente.
            }
        }

        if ($dataFim !== '') {
            try {
                $logsQuery->where('created_at', '<=', Carbon::parse($dataFim)->endOfDay());
            } catch (\Throwable) {
                // Ignora datas inválidas para manter o filtro resiliente.
            }
        }

        if (! $searchTooShort && $search !== '') {
            $logsQuery->where(function ($query) use ($search): void {
                $query
                    ->where('alteracao', 'like', "%{$search}%")
                    ->orWhere('ip', 'like', "%{$search}%")
                    ->orWhere('browser', 'like', "%{$search}%")
                    ->orWhere('objeto_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search): void {
                        $userQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $logs = $logsQuery
            ->paginate(25)
            ->withQueryString();

        $logs->getCollection()->transform(function (ActivityLog $log): ActivityLog {
            [$acao, $alteracoes] = $this->parseAlteracao($log->alteracao);

            $alteracoesFormatadas = [];
            foreach ($alteracoes as $campo => $valor) {
                $alteracoesFormatadas[] = [
                    'campo' => self::FIELD_LABELS[$campo] ?? str_replace('_', ' ', $campo),
                    'valor' => $this->formatLogValue($campo, $valor),
                ];
            }

            $log->setAttribute('acao_label', $this->translateAction($acao));
            $log->setAttribute('modulo_label', self::MODULE_LABELS[$log->modulo] ?? $log->modulo);
            $log->setAttribute('browser_label', $this->browserLabel($log->browser));
            $log->setAttribute('alteracoes_formatadas', $alteracoesFormatadas);

            return $log;
        });

        $modulos = ActivityLog::query()
            ->select('modulo')
            ->whereNotNull('modulo')
            ->distinct()
            ->orderBy('modulo')
            ->pluck('modulo');

        return view('admin.logs.index', [
            'logs' => $logs,
            'modulos' => $modulos,
            'moduloFilter' => $modulo,
            'utilizadorFilter' => $utilizador,
            'objetoIdFilter' => $objetoId,
            'dataInicioFilter' => $dataInicio,
            'dataFimFilter' => $dataFim,
            'search' => $search,
            'searchTooShort' => $searchTooShort,
        ]);
    }

    /**
     * @return array{0:string,1:array<string,mixed>}
     */
    private function parseAlteracao(?string $alteracao): array
    {
        $texto = trim((string) $alteracao);
        if ($texto === '') {
            return ['', []];
        }

        [$acao, $alteracoesRaw] = array_pad(explode(' | alterações: ', $texto, 2), 2, null);
        $decoded = is_string($alteracoesRaw) ? json_decode($alteracoesRaw, true) : null;

        if (is_array($decoded)) {
            return [trim($acao), $decoded];
        }

        return [$texto, []];
    }

    private function formatLogValue(string $campo, mixed $valor): string
    {
        if ($this->isSensitiveField($campo)) {
            return '[protegido]';
        }

        if ($valor === null) {
            return 'nulo';
        }

        if (is_bool($valor)) {
            return $valor ? 'sim' : 'não';
        }

        if (is_scalar($valor)) {
            return (string) $valor;
        }

        return (string) json_encode($valor, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function translateAction(string $acao): string
    {
        $normalized = Str::lower(trim($acao));

        if ($normalized === '') {
            return 'Evento';
        }

        return self::ACTION_LABELS[$normalized] ?? Str::ucfirst($acao);
    }

    private function isSensitiveField(string $campo): bool
    {
        $normalized = Str::lower($campo);

        foreach (self::SENSITIVE_FIELD_PATTERNS as $pattern) {
            if (Str::contains($normalized, $pattern)) {
                return true;
            }
        }

        return false;
    }

    private function browserLabel(?string $userAgent): string
    {
        if ($userAgent === null || $userAgent === '') {
            return '-';
        }

        return match (true) {
            str_contains($userAgent, 'Edg/') => 'Edge',
            str_contains($userAgent, 'Chrome/') && ! str_contains($userAgent, 'Edg/') => 'Chrome',
            str_contains($userAgent, 'Firefox/') => 'Firefox',
            str_contains($userAgent, 'Safari/') && ! str_contains($userAgent, 'Chrome/') => 'Safari',
            str_contains($userAgent, 'Opera') || str_contains($userAgent, 'OPR/') => 'Opera',
            default => 'Outro',
        };
    }
}
