<?php

namespace App\Livewire;

use App\Models\Livro;
use App\Models\Requisicao;
use Livewire\Component;
use Livewire\WithPagination;

class LivrosRequisicaoTable extends Component
{
    public ?int $requisicaoParaDevolver = null;
    public bool $mostrarPopupDevolucao = false;
    public ?int $requisicaoParaAceitar = null;
    public bool $mostrarPopupAceitar = false;
    public ?int $requisicaoParaRecusar = null;
    public bool $mostrarPopupRecusar = false;
    /** @var array<int> */
    public array $pedidosDevolucaoEnviados = [];
    public function aceitarRequisicao($id)
    {
        $this->requisicaoParaAceitar = $id;
        $this->mostrarPopupAceitar = true;
    }

    public function confirmarAceitarRequisicao()
    {
        $id = $this->requisicaoParaAceitar;
        $requisicao = Requisicao::find($id);
        if ($requisicao) {
            $requisicao->estado_devolucao = 'aceite';
            $requisicao->devolvido_em = now();
            $requisicao->save();
        }
        $this->mostrarPopupAceitar = false;
        $this->requisicaoParaAceitar = null;
        $this->reset();
        $this->dispatch('notify', message: 'Requisição aceite!');
    }

    public function recusarRequisicao($id)
    {
        $this->requisicaoParaRecusar = $id;
        $this->mostrarPopupRecusar = true;
    }

    public function confirmarRecusarRequisicao()
    {
        $id = $this->requisicaoParaRecusar;
        $requisicao = Requisicao::find($id);
        if ($requisicao) {
            $requisicao->estado_devolucao = 'recusado';
            $requisicao->save();
        }
        $this->mostrarPopupRecusar = false;
        $this->requisicaoParaRecusar = null;
        $this->reset();
        $this->dispatch('notify', message: 'Requisição recusada!');
    }


    public function pedirDevolucao($id)
    {
        $this->requisicaoParaDevolver = $id;
        $this->mostrarPopupDevolucao = true;
    }

        public function confirmarDevolucao()
        {
            $id = $this->requisicaoParaDevolver;
            $requisicao = Requisicao::find($id);
            $user = auth()->user();
            if ($requisicao && $requisicao->devolvido_em === null) {
                if ($user && $user->isAdmin()) {
                    $requisicao->estado_devolucao = 'aceite';
                    $requisicao->devolvido_em = now();
                    $requisicao->save();
                    $this->dispatch('notify', message: 'Livro devolvido com sucesso!');
                } else {
                    if ($requisicao->pedido_devolucao_em === null) {
                        $requisicao->pedido_devolucao_em = now();
                        $requisicao->estado_devolucao = 'pendente';
                        $requisicao->save();
                    }
                    $this->dispatch('notify', message: 'Pedido de devolução enviado!');
                }
            }
            $this->mostrarPopupDevolucao = false;
            $this->requisicaoParaDevolver = null;
            $this->reset();
        }

    use WithPagination;


    // Filtros gerais
    public string $search = '';
    public string $estado = '';
    public string $dataRequisicaoInicio = '';
    public string $dataRequisicaoFim = '';
    public string $dataPrevistaInicio = '';
    public string $dataPrevistaFim = '';
    public string $dataDevolucaoInicio = '';
    public string $dataDevolucaoFim = '';
    public string $precoMin = '';
    public string $precoMax = '';
    public string $sortDirection = 'normal';

    // Filtros para adminRequisicoes
    public string $adminSearch = '';
    public string $adminEstado = '';
    public string $adminDataRequisicaoInicio = '';
    public string $adminDataRequisicaoFim = '';
    public string $adminDataPrevistaInicio = '';
    public string $adminDataPrevistaFim = '';
    public string $adminDataDevolucaoInicio = '';
    public string $adminDataDevolucaoFim = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'estado' => ['except' => ''],
        'dataRequisicaoInicio' => ['except' => ''],
        'dataRequisicaoFim' => ['except' => ''],
        'dataPrevistaInicio' => ['except' => ''],
        'dataPrevistaFim' => ['except' => ''],
        'dataDevolucaoInicio' => ['except' => ''],
        'dataDevolucaoFim' => ['except' => ''],
        'precoMin' => ['except' => ''],
        'precoMax' => ['except' => ''],
        'sortDirection' => ['except' => 'normal'],
    ];

    public function mount(bool $isAdmin = false): void
    {
        $this->isAdmin = $isAdmin;
    }

    public function updating($name)
    {
        if (in_array($name, ['search', 'precoMin', 'precoMax'], true)) {
            $this->resetPage();
        }
    }

    public function sortBy(): void
    {
        if ($this->sortDirection === 'asc') {
            $this->sortDirection = 'desc';
        } elseif ($this->sortDirection === 'desc') {
            $this->sortDirection = 'normal';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function setSortPreset(string $preset): void
    {
        $this->sortDirection = match ($preset) {
            'az' => 'asc',
            'za' => 'desc',
            default => 'normal',
        };

        $this->resetPage();
    }

    protected function normalizePrice(string $value): ?float
    {
        $normalized = str_replace(',', '.', trim($value));

        if ($normalized === '' || !is_numeric($normalized)) {
            return null;
        }

        return (float) $normalized;
    }

    protected function getLivrosQuery()
    {
        $search = trim($this->search);
        $precoMin = $this->normalizePrice($this->precoMin);
        $precoMax = $this->normalizePrice($this->precoMax);

        $query = Livro::query()
            ->with(['editora', 'autores', 'requisicoes' => function ($q) {
                $q->whereNull('devolvido_em');
            }])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('isbn', 'like', "%{$search}%")
                        ->orWhere('nome', 'like', "%{$search}%")
                        ->orWhereHas('editora', fn ($e) => $e->where('nome', 'like', "%{$search}%"))
                        ->orWhereHas('autores', fn ($a) => $a->where('nome', 'like', "%{$search}%"));
                });
            })
            ->when($precoMin !== null, fn ($q) => $q->where('preco', '>=', $precoMin))
            ->when($precoMax !== null, fn ($q) => $q->where('preco', '<=', $precoMax));

        if ($this->sortDirection !== 'normal') {
            $query->orderBy('nome', $this->sortDirection);
        } else {
            $query->orderBy('id', 'asc');
        }

        return $query;
    }

    public function requisitar(Livro $livro)
    {
        // Verificar se o utilizador já tem 3 livros requisitados em simultâneo
        $user = auth()->user();
        $ativos = Requisicao::query()
            ->where('user_id', $user->id)
            ->whereNull('devolvido_em')
            ->count();
        if ($ativos >= 3) {
            $this->dispatch('showLimitPopup');
            return;
        }
        $user = auth()->user();

        // Verificar se o livro já está requisitado e não devolvido
        $jaRequisitado = Requisicao::query()
            ->where('livro_id', $livro->id)
            ->whereNull('devolvido_em')
            ->exists();

        if ($jaRequisitado) {
            $this->addError('ja_requisitado', 'Este livro já está requisitado.');
            return;
        }


        $requisitadoEm = now();
        $fimPrevistoEm = $requisitadoEm->copy()->addDays(5);

        // Gerar o próximo número sequencial
        $ultimoNumero = Requisicao::max('numero');
        $proximoNumero = $ultimoNumero ? ((int)$ultimoNumero + 1) : 1;

        $requisicao = Requisicao::query()->create([
            'numero' => $proximoNumero,
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'requisitado_em' => $requisitadoEm,
            'fim_previsto_em' => $fimPrevistoEm,
        ]);

        // Enviar email para o cidadão
        try {
            \Mail::to($user->email)->send(new \App\Mail\NovaRequisicaoMail($requisicao));
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar email de requisição para cidadão: ' . $e->getMessage());
        }

        // Enviar email para todos os admins
        try {
            $admins = \App\Models\User::where('role', \App\Models\User::ROLE_ADMIN)->pluck('email');
            foreach ($admins as $adminEmail) {
                \Mail::to($adminEmail)->send(new \App\Mail\NovaRequisicaoMail($requisicao));
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar email de requisição para admins: ' . $e->getMessage());
        }

        $this->dispatch('notify', message: 'Livro requisitado com sucesso!');
    }

    public function render()
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();

        // Indicadores para a tabela de requisições do admin (apenas as do próprio admin)
        $totalAdminAtivas = null;
        $totalAdminUltimos30Dias = null;
        $totalAdminEntreguesHoje = null;
        $adminId = $user->id;
        if ($isAdmin) {
            $totalAdminAtivas = Requisicao::where('user_id', $adminId)->whereNull('devolvido_em')->count();
            $totalAdminUltimos30Dias = Requisicao::where('user_id', $adminId)->where('requisitado_em', '>=', now()->subDays(30))->count();
            $totalAdminEntreguesHoje = Requisicao::where('user_id', $adminId)->whereDate('devolvido_em', now()->toDateString())->count();
        }

        // Para admin: requisições feitas pelo próprio admin
        $adminRequisicoes = collect();
        $adminId = $user->id;
        if ($isAdmin) {
            $adminRequisicoesQuery = Requisicao::with(['livro.editora', 'user'])
                ->where('user_id', $adminId)
                ->when($this->adminSearch !== '', function ($q) {
                    $search = $this->adminSearch;
                    $q->where(function ($sub) use ($search) {
                        $sub->whereHas('livro', function ($livroQ) use ($search) {
                            $livroQ->where('nome', 'like', "%{$search}%")
                                ->orWhere('isbn', 'like', "%{$search}%");
                        })
                        ->orWhereHas('user', fn ($userQ) => $userQ->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                        ->orWhereHas('livro.editora', fn ($editoraQ) => $editoraQ->where('nome', 'like', "%{$search}%"));
                    });
                })
                ->when($this->adminEstado !== '', function ($q) {
                    if ($this->adminEstado === 'requisitado') {
                        $q->whereNull('devolvido_em')->where(function($subQ) {
                            $subQ->whereNull('estado_devolucao')->orWhere('estado_devolucao', '');
                        });
                    } elseif ($this->adminEstado === 'aceite') {
                        $q->where('estado_devolucao', 'aceite');
                    } elseif ($this->adminEstado === 'recusado') {
                        $q->where('estado_devolucao', 'recusado');
                    }
                })
                ->when($this->adminDataRequisicaoInicio !== '', fn($q) => $q->whereDate('requisitado_em', '>=', $this->adminDataRequisicaoInicio))
                ->when($this->adminDataRequisicaoFim !== '', fn($q) => $q->whereDate('requisitado_em', '<=', $this->adminDataRequisicaoFim))
                ->when($this->adminDataPrevistaInicio !== '', fn($q) => $q->whereDate('fim_previsto_em', '>=', $this->adminDataPrevistaInicio))
                ->when($this->adminDataPrevistaFim !== '', fn($q) => $q->whereDate('fim_previsto_em', '<=', $this->adminDataPrevistaFim))
                ->when($this->adminDataDevolucaoInicio !== '', fn($q) => $q->whereDate('devolvido_em', '>=', $this->adminDataDevolucaoInicio))
                ->when($this->adminDataDevolucaoFim !== '', fn($q) => $q->whereDate('devolvido_em', '<=', $this->adminDataDevolucaoFim));

            $adminRequisicoes = $adminRequisicoesQuery->orderByDesc('requisitado_em')->get();
        }

        // Query base para histórico (inclui as do admin devolvidas)
        $historicoQuery = Requisicao::with(['livro.editora', 'user'])
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id))
            ->when($isAdmin, function($q) use ($adminId) {
                $q->where(function($subQ) use ($adminId) {
                    $subQ->where('user_id', '!=', $adminId)
                        ->orWhere(function($adminQ) use ($adminId) {
                            $adminQ->where('user_id', $adminId)
                                ->where('estado_devolucao', 'aceite')
                                ->whereNotNull('devolvido_em');
                        });
                });
            })
            ->when($this->search !== '', function ($q) {
                $search = $this->search;
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('livro', function ($livroQ) use ($search) {
                        $livroQ->where('nome', 'like', "%{$search}%")
                            ->orWhere('isbn', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', fn ($userQ) => $userQ->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('livro.editora', fn ($editoraQ) => $editoraQ->where('nome', 'like', "%{$search}%"));
                });
            })
            ->when($this->estado !== '', function ($q) {
                if ($this->estado === 'requisitado') {
                    $q->whereNull('devolvido_em')->where(function($subQ) {
                        $subQ->whereNull('estado_devolucao')->orWhere('estado_devolucao', '');
                    });
                } elseif ($this->estado === 'aceite') {
                    $q->where('estado_devolucao', 'aceite');
                } elseif ($this->estado === 'recusado') {
                    $q->where('estado_devolucao', 'recusado');
                }
            })
            ->when($this->dataRequisicaoInicio !== '', fn($q) => $q->whereDate('requisitado_em', '>=', $this->dataRequisicaoInicio))
            ->when($this->dataRequisicaoFim !== '', fn($q) => $q->whereDate('requisitado_em', '<=', $this->dataRequisicaoFim))
            ->when($this->dataPrevistaInicio !== '', fn($q) => $q->whereDate('fim_previsto_em', '>=', $this->dataPrevistaInicio))
            ->when($this->dataPrevistaFim !== '', fn($q) => $q->whereDate('fim_previsto_em', '<=', $this->dataPrevistaFim))
            ->when($this->dataDevolucaoInicio !== '', fn($q) => $q->whereDate('devolvido_em', '>=', $this->dataDevolucaoInicio))
            ->when($this->dataDevolucaoFim !== '', fn($q) => $q->whereDate('devolvido_em', '<=', $this->dataDevolucaoFim));

        $historico = $historicoQuery->orderByDesc('requisitado_em')->get();


        // Indicadores por utilizador (admin vê todos)
        if ($isAdmin) {
            $totalAtivas = Requisicao::whereNull('devolvido_em')->count();
            $totalUltimos30Dias = Requisicao::where('requisitado_em', '>=', now()->subDays(30))->count();
            $totalEntreguesHoje = Requisicao::whereDate('devolvido_em', now()->toDateString())->count();
        } else {
            $totalAtivas = Requisicao::where('user_id', $user->id)->whereNull('devolvido_em')->count();
            $totalUltimos30Dias = Requisicao::where('user_id', $user->id)->where('requisitado_em', '>=', now()->subDays(30))->count();
            $totalEntreguesHoje = Requisicao::where('user_id', $user->id)->whereDate('devolvido_em', now()->toDateString())->count();
        }

        // Indicadores globais (todas as requisições, só para admin)
        $totalTodasAtivas = null;
        $totalTodasUltimos30Dias = null;
        $totalTodasEntreguesHoje = null;
        if ($isAdmin) {
            $totalTodasAtivas = Requisicao::whereNull('devolvido_em')->count();
            $totalTodasUltimos30Dias = Requisicao::where('requisitado_em', '>=', now()->subDays(30))->count();
            $totalTodasEntreguesHoje = Requisicao::whereDate('devolvido_em', now()->toDateString())->count();
        }

        return view('livewire.livros-requisicao-table', [
            'historico' => $historico,
            'adminRequisicoes' => $adminRequisicoes,
            'pedidosDevolucaoEnviados' => $this->pedidosDevolucaoEnviados,
            'isAdmin' => $isAdmin,
            'totalAtivas' => $totalAtivas,
            'totalUltimos30Dias' => $totalUltimos30Dias,
            'totalEntreguesHoje' => $totalEntreguesHoje,
            'totalAdminAtivas' => $totalAdminAtivas,
            'totalAdminUltimos30Dias' => $totalAdminUltimos30Dias,
            'totalAdminEntreguesHoje' => $totalAdminEntreguesHoje,
            'totalTodasAtivas' => $totalTodasAtivas,
            'totalTodasUltimos30Dias' => $totalTodasUltimos30Dias,
            'totalTodasEntreguesHoje' => $totalTodasEntreguesHoje,
        ]);
    }
}
