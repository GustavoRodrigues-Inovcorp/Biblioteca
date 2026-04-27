<?php

namespace App\Livewire;

use App\Models\Requisicao;
use App\Models\Review;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Componente Livewire: RequisicaoTable
 *
 * Gere a tabela de requisições de livros, tanto para admin como para utilizador normal.
 * Permite aceitar, recusar, pedir devolução, submeter reviews e aplicar filtros.
 * Mostra popups de confirmação e estatísticas.
 */
class RequisicaoTable extends Component
{
    // Propriedades para popups e controlo de ações
    public ?int $requisicaoParaDevolver = null;
    public bool $mostrarPopupDevolucao = false;
    public ?int $requisicaoParaAceitar = null;
    public bool $mostrarPopupAceitar = false;
    public ?int $requisicaoParaRecusar = null;
    public bool $mostrarPopupRecusar = false;
    /** @var array<int> */
    public array $pedidosDevolucaoEnviados = [];
    public $minhasRequisicoes = [];

    // Popup de review
    public bool $mostrarPopupReview = false;
    public ?int $livroParaReview = null;
    public ?int $requisicaoParaReview = null;
    public int $reviewRating = 0;
    public string $reviewComentario = '';

    /**
     * Inicia o popup para aceitar uma requisição.
     */
    public function aceitarRequisicao($id)
    {
        $this->requisicaoParaAceitar = $id;
        $this->mostrarPopupAceitar = true;
    }

    /**
     * Confirma a aceitação da requisição, marca como devolvida e notifica alertas.
     */
    public function confirmarAceitarRequisicao()
    {
        $id = $this->requisicaoParaAceitar;
        $requisicao = Requisicao::find($id);
        if ($requisicao) {
            $requisicao->estado_devolucao = 'aceite';
            $requisicao->devolvido_em = now()->toDateTimeString();
            $requisicao->save();
            // Notificar alertas se o livro ficou disponível
            $livro = $requisicao->livro;
            if ($livro && $livro->isDisponivel()) {
                $livro->notificarAlertasDisponivel();
            }
        }
        $this->mostrarPopupAceitar = false;
        $this->requisicaoParaAceitar = null;
        // Recarregar histórico
        $this->atualizarHistorico();
        $this->dispatch('notify', message: 'Requisição aceite!');
    }

    /**
     * Inicia o popup para recusar uma requisição.
     */
    public function recusarRequisicao($id)
    {
        $this->requisicaoParaRecusar = $id;
        $this->mostrarPopupRecusar = true;
    }

    /**
     * Confirma a recusa da requisição.
     */
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
        // Recarregar histórico
        $this->atualizarHistorico();
        $this->dispatch('notify', message: 'Requisição recusada!');
    }

    /**
     * Atualiza o array $historico conforme o utilizador (admin ou não)
     */
    /**
     * Atualiza o histórico de requisições conforme o tipo de utilizador e filtros.
     */
    public function atualizarHistorico()
    {
        $user = auth()->user();
        if ($this->isAdmin) {
            $query = Requisicao::with(['livro.editora', 'user']);
            if ($this->adminEstado) {
                if ($this->adminEstado === 'requisitado') {
                    $query->whereNull('devolvido_em')->where(function($subQ) {
                        $subQ->whereNull('estado_devolucao')->orWhere('estado_devolucao', '');
                    });
                } elseif ($this->adminEstado === 'pendente') {
                    $query->where('estado_devolucao', 'pendente');
                } elseif ($this->adminEstado === 'aceite') {
                    $query->where('estado_devolucao', 'aceite');
                } elseif ($this->adminEstado === 'recusado') {
                    $query->where('estado_devolucao', 'recusado');
                }
            }
            if ($this->adminDataRequisicaoInicio) {
                $query->whereDate('requisitado_em', '>=', $this->adminDataRequisicaoInicio);
            }
            if ($this->adminDataRequisicaoFim) {
                $query->whereDate('requisitado_em', '<=', $this->adminDataRequisicaoFim);
            }
            if ($this->adminDataPrevistaInicio) {
                $query->whereDate('fim_previsto_em', '>=', $this->adminDataPrevistaInicio);
            }
            if ($this->adminDataPrevistaFim) {
                $query->whereDate('fim_previsto_em', '<=', $this->adminDataPrevistaFim);
            }
            if ($this->adminDataDevolucaoInicio) {
                $query->whereDate('devolvido_em', '>=', $this->adminDataDevolucaoInicio);
            }
            if ($this->adminDataDevolucaoFim) {
                $query->whereDate('devolvido_em', '<=', $this->adminDataDevolucaoFim);
            }
            if ($this->search !== '') {
                $search = $this->search;
                $query->where(function ($sub) use ($search) {
                    $sub->whereHas('livro', function ($livroQ) use ($search) {
                        $livroQ->where('nome', 'like', "%{$search}%")
                            ->orWhere('isbn', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', fn ($userQ) => $userQ->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('livro.editora', fn ($editoraQ) => $editoraQ->where('nome', 'like', "%{$search}%"));
                });
            }
            $this->historico = $query->orderByDesc('requisitado_em')->get();

            // Popular minhasRequisicoes para o admin
            $this->minhasRequisicoes = Requisicao::with(['livro.editora'])
                ->where('user_id', $user->id)
                ->orderByDesc('requisitado_em')
                ->get()
                ->all();
        } else {
            $query = Requisicao::with(['livro.editora'])
                ->where('user_id', $user->id);
            if ($this->estado) {
                if ($this->estado === 'requisitado') {
                    $query->whereNull('devolvido_em')->where(function($subQ) {
                        $subQ->whereNull('estado_devolucao')->orWhere('estado_devolucao', '');
                    });
                } elseif ($this->estado === 'pendente') {
                    $query->where('estado_devolucao', 'pendente');
                } elseif ($this->estado === 'aceite') {
                    $query->where('estado_devolucao', 'aceite');
                } elseif ($this->estado === 'recusado') {
                    $query->where('estado_devolucao', 'recusado');
                }
            }
            if ($this->dataRequisicaoInicio) {
                $query->whereDate('requisitado_em', '>=', $this->dataRequisicaoInicio);
            }
            if ($this->dataRequisicaoFim) {
                $query->whereDate('requisitado_em', '<=', $this->dataRequisicaoFim);
            }
            if ($this->dataPrevistaInicio) {
                $query->whereDate('fim_previsto_em', '>=', $this->dataPrevistaInicio);
            }
            if ($this->dataPrevistaFim) {
                $query->whereDate('fim_previsto_em', '<=', $this->dataPrevistaFim);
            }
            if ($this->dataDevolucaoInicio) {
                $query->whereDate('devolvido_em', '>=', $this->dataDevolucaoInicio);
            }
            if ($this->dataDevolucaoFim) {
                $query->whereDate('devolvido_em', '<=', $this->dataDevolucaoFim);
            }
            if ($this->search !== '') {
                $search = $this->search;
                $query->where(function ($sub) use ($search) {
                    $sub->whereHas('livro', function ($livroQ) use ($search) {
                        $livroQ->where('nome', 'like', "%{$search}%")
                            ->orWhere('isbn', 'like', "%{$search}%");
                    })
                    ->orWhereHas('livro.editora', fn ($editoraQ) => $editoraQ->where('nome', 'like', "%{$search}%"));
                });
            }
            $this->historico = $query->orderByDesc('requisitado_em')->get();
        }
    }

    /**
     * Inicia o popup para pedir devolução de um livro.
     */
    public function pedirDevolucao($id)
    {
        $this->requisicaoParaDevolver = $id;
        $this->mostrarPopupDevolucao = true;
    }

    /**
     * Confirma a devolução do livro (admin ou cidadão).
     */
    public function confirmarDevolucao()
    {
        $id = $this->requisicaoParaDevolver;
        $requisicao = Requisicao::find($id);
        $user = auth()->user();
        if ($requisicao && $requisicao->devolvido_em === null) {
            if ($user && $user->isAdmin()) {
                $requisicao->estado_devolucao = 'aceite';
                $requisicao->devolvido_em = now()->toDateTimeString();
                $requisicao->save();
                $this->dispatch('notify', message: 'Livro devolvido com sucesso!');
            } else {
                if ($requisicao->pedido_devolucao_em === null) {
                    $requisicao->pedido_devolucao_em = now()->toDateTimeString();
                    $requisicao->estado_devolucao = 'pendente';
                    $requisicao->save();
                }
                $this->dispatch('notify', message: 'Pedido de devolução enviado!');
                // Mostrar popup de review imediatamente para cidadãos
                $this->livroParaReview = $requisicao->livro_id;
                $this->requisicaoParaReview = $requisicao->id;
                $this->mostrarPopupReview = true;
            }
        }
        $this->mostrarPopupDevolucao = false;
        $this->requisicaoParaDevolver = null;
        $this->atualizarHistorico();
    }

    /**
     * Submete uma review após devolução do livro.
     */
    public function submeterReview()
    {
        $this->validate([
            'reviewRating' => 'required|integer|min:1|max:5',
            'reviewComentario' => 'nullable|string|max:1000',
        ]);
        $review = Review::create([
            'user_id' => auth()->id(),
            'livro_id' => $this->livroParaReview,
            'requisicao_id' => $this->requisicaoParaReview,
            'rating' => $this->reviewRating,
            'comentario' => $this->reviewComentario,
            'estado' => 'suspenso',
        ]);

        // Enviar email para todos os admins
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            \Mail::to($admin->email)->send(new \App\Mail\NovaReviewMail($review));
        }

        $this->mostrarPopupReview = false;
        $this->livroParaReview = null;
        $this->requisicaoParaReview = null;
        $this->reviewRating = 0;
        $this->reviewComentario = '';
        $this->dispatch('notify', message: 'Review submetida com sucesso!');
    }


    // Propriedades para estatísticas/admin
    public $adminSearch = '';
    public $adminRequisicoes = null;
    public $totalAdminAtivas = null;
    public $totalAdminUltimos30Dias = null;
    public $totalAdminEntreguesHoje = null;
    public $adminEstado = '';
    public $adminDataRequisicaoInicio = null;
    public $adminDataRequisicaoFim = null;
    public $adminDataPrevistaInicio = null;
    public $adminDataPrevistaFim = null;
    public $adminDataDevolucaoInicio = null;
    public $adminDataDevolucaoFim = null;
    public $historico = [];
    // Filtros para cidadão
    public $estado = '';
    public $dataRequisicaoInicio = null;
    public $dataRequisicaoFim = null;
    public $dataPrevistaInicio = null;
    public $dataPrevistaFim = null;
    public $dataDevolucaoInicio = null;
    public $dataDevolucaoFim = null;


    use WithPagination;

    public bool $isAdmin = false;
    public ?int $userId = null;
    public string $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    /**
     * Inicializa o componente, define se é admin e carrega o histórico.
     */
    public function mount(bool $isAdmin = false, ?int $userId = null): void
    {
        $this->isAdmin = $isAdmin;
        $this->userId = $userId;
        $this->atualizarHistorico();
    }

    /**
     * Reseta a páginação ao atualizar a pesquisa.
     */
    public function updating($name)
    {
        if ($name === 'search') {
            $this->resetPage();
        }
    }

    #[Computed]
    /**
     * Computed property: lista paginada de requisições conforme filtros.
     */
    public function requisicoes()
    {
        $search = trim($this->search);

        return Requisicao::query()
            ->with(['livro.editora', 'user'])
            ->when(!$this->isAdmin && $this->userId !== null, fn ($q) => $q->where('user_id', $this->userId))
            ->when($search !== '', function ($q) use ($search) {
                $q->whereHas('livro', fn ($subQ) => $subQ->where('nome', 'like', "%{$search}%"))
                    ->orWhereHas('user', fn ($subQ) => $subQ->where('name', 'like', "%{$search}%"));
            })
            ->orderByDesc('requisitado_em')
            ->paginate(15);
    }

    /**
     * Marca uma requisição como devolvida.
     */
    public function devolver(Requisicao $requisicao): void
    {
        $user = auth()->user();

        if (!$this->isAdmin && $requisicao->user_id !== $user->id) {
            abort(403);
        }

        if ($requisicao->devolvido_em !== null) {
            $this->dispatch('notify', message: 'Esta requisição já estava devolvida.');
            return;
        }

        $requisicao->update([
            'devolvido_em' => now(),
        ]);

        $this->dispatch('notify', message: 'Livro devolvido com sucesso.');
    }

    /**
     * Renderiza a view do componente, carregando dados e estatísticas.
     */
    public function render()
    {
        $user = auth()->user();

        // Se for admin, mostrar todas as requisições (com filtros)
        if ($this->isAdmin) {
            // Tabela de todas as requisições (admin)
            $query = Requisicao::with(['livro.editora', 'user']);
            if ($this->adminEstado) {
                if ($this->adminEstado === 'requisitado') {
                    $query->whereNull('devolvido_em')->where(function($subQ) {
                        $subQ->whereNull('estado_devolucao')->orWhere('estado_devolucao', '');
                    });
                } elseif ($this->adminEstado === 'pendente') {
                    $query->where('estado_devolucao', 'pendente');
                } elseif ($this->adminEstado === 'aceite') {
                    $query->where('estado_devolucao', 'aceite');
                } elseif ($this->adminEstado === 'recusado') {
                    $query->where('estado_devolucao', 'recusado');
                }
            }
            if ($this->adminDataRequisicaoInicio) {
                $query->whereDate('requisitado_em', '>=', $this->adminDataRequisicaoInicio);
            }
            if ($this->adminDataRequisicaoFim) {
                $query->whereDate('requisitado_em', '<=', $this->adminDataRequisicaoFim);
            }
            if ($this->adminDataPrevistaInicio) {
                $query->whereDate('fim_previsto_em', '>=', $this->adminDataPrevistaInicio);
            }
            if ($this->adminDataPrevistaFim) {
                $query->whereDate('fim_previsto_em', '<=', $this->adminDataPrevistaFim);
            }
            if ($this->adminDataDevolucaoInicio) {
                $query->whereDate('devolvido_em', '>=', $this->adminDataDevolucaoInicio);
            }
            if ($this->adminDataDevolucaoFim) {
                $query->whereDate('devolvido_em', '<=', $this->adminDataDevolucaoFim);
            }
            if ($this->adminSearch !== '') {
                $search = $this->adminSearch;
                $query->where(function ($sub) use ($search) {
                    $sub->whereHas('livro', function ($livroQ) use ($search) {
                        $livroQ->where('nome', 'like', "%{$search}%")
                            ->orWhere('isbn', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', fn ($userQ) => $userQ->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('livro.editora', fn ($editoraQ) => $editoraQ->where('nome', 'like', "%{$search}%"));
                });
            }
            $this->historico = $query->orderByDesc('requisitado_em')->get();

            // Indicadores globais
            $this->totalAdminAtivas = Requisicao::whereNull('devolvido_em')->count();
            $this->totalAdminUltimos30Dias = Requisicao::where('requisitado_em', '>=', now()->subDays(30))->count();
            $this->totalAdminEntreguesHoje = Requisicao::whereDate('devolvido_em', now()->toDateString())->count();

            // Tabela de "minhas requisições" (admin)
            $minhasQuery = Requisicao::with(['livro.editora'])
                ->where('user_id', $user->id);
            if ($this->estado) {
                if ($this->estado === 'requisitado') {
                    $minhasQuery->whereNull('devolvido_em')->where(function($subQ) {
                        $subQ->whereNull('estado_devolucao')->orWhere('estado_devolucao', '');
                    });
                } elseif ($this->estado === 'pendente') {
                    $minhasQuery->where('estado_devolucao', 'pendente');
                } elseif ($this->estado === 'aceite') {
                    $minhasQuery->where('estado_devolucao', 'aceite');
                } elseif ($this->estado === 'recusado') {
                    $minhasQuery->where('estado_devolucao', 'recusado');
                }
            }
            if ($this->dataRequisicaoInicio) {
                $minhasQuery->whereDate('requisitado_em', '>=', $this->dataRequisicaoInicio);
            }
            if ($this->dataRequisicaoFim) {
                $minhasQuery->whereDate('requisitado_em', '<=', $this->dataRequisicaoFim);
            }
            if ($this->dataPrevistaInicio) {
                $minhasQuery->whereDate('fim_previsto_em', '>=', $this->dataPrevistaInicio);
            }
            if ($this->dataPrevistaFim) {
                $minhasQuery->whereDate('fim_previsto_em', '<=', $this->dataPrevistaFim);
            }
            if ($this->dataDevolucaoInicio) {
                $minhasQuery->whereDate('devolvido_em', '>=', $this->dataDevolucaoInicio);
            }
            if ($this->dataDevolucaoFim) {
                $minhasQuery->whereDate('devolvido_em', '<=', $this->dataDevolucaoFim);
            }
            if ($this->search !== '') {
                $search = $this->search;
                $minhasQuery->where(function ($sub) use ($search) {
                    $sub->whereHas('livro', function ($livroQ) use ($search) {
                        $livroQ->where('nome', 'like', "%{$search}%")
                            ->orWhere('isbn', 'like', "%{$search}%");
                    })
                    ->orWhereHas('livro.editora', fn ($editoraQ) => $editoraQ->where('nome', 'like', "%{$search}%"));
                });
            }
            $this->minhasRequisicoes = $minhasQuery->orderByDesc('requisitado_em')->get();
        } else {
            // Utilizador normal: só as suas requisições
            $query = Requisicao::with(['livro.editora'])
                ->where('user_id', $user->id);
            if ($this->estado) {
                if ($this->estado === 'requisitado') {
                    $query->whereNull('devolvido_em')->where(function($subQ) {
                        $subQ->whereNull('estado_devolucao')->orWhere('estado_devolucao', '');
                    });
                } elseif ($this->estado === 'pendente') {
                    $query->where('estado_devolucao', 'pendente');
                } elseif ($this->estado === 'aceite') {
                    $query->where('estado_devolucao', 'aceite');
                } elseif ($this->estado === 'recusado') {
                    $query->where('estado_devolucao', 'recusado');
                }
            }
            if ($this->dataRequisicaoInicio) {
                $query->whereDate('requisitado_em', '>=', $this->dataRequisicaoInicio);
            }
            if ($this->dataRequisicaoFim) {
                $query->whereDate('requisitado_em', '<=', $this->dataRequisicaoFim);
            }
            if ($this->dataPrevistaInicio) {
                $query->whereDate('fim_previsto_em', '>=', $this->dataPrevistaInicio);
            }
            if ($this->dataPrevistaFim) {
                $query->whereDate('fim_previsto_em', '<=', $this->dataPrevistaFim);
            }
            if ($this->dataDevolucaoInicio) {
                $query->whereDate('devolvido_em', '>=', $this->dataDevolucaoInicio);
            }
            if ($this->dataDevolucaoFim) {
                $query->whereDate('devolvido_em', '<=', $this->dataDevolucaoFim);
            }
            if ($this->search !== '') {
                $search = $this->search;
                $query->where(function ($sub) use ($search) {
                    $sub->whereHas('livro', function ($livroQ) use ($search) {
                        $livroQ->where('nome', 'like', "%{$search}%")
                            ->orWhere('isbn', 'like', "%{$search}%");
                    })
                    ->orWhereHas('livro.editora', fn ($editoraQ) => $editoraQ->where('nome', 'like', "%{$search}%"));
                });
            }
            $this->historico = $query->orderByDesc('requisitado_em')->get();
            // Para cidadão, minhasRequisicoes é igual ao histórico
            $this->minhasRequisicoes = $this->historico;

            // Indicadores do próprio utilizador
            $this->totalAdminAtivas = Requisicao::where('user_id', $user->id)->whereNull('devolvido_em')->count();
            $this->totalAdminUltimos30Dias = Requisicao::where('user_id', $user->id)->where('requisitado_em', '>=', now()->subDays(30))->count();
            $this->totalAdminEntreguesHoje = Requisicao::where('user_id', $user->id)->whereDate('devolvido_em', now()->toDateString())->count();
        }

        return view('livewire.requisicao-table', [
            'minhasRequisicoes' => $this->minhasRequisicoes,
        ]);
    }

}
