<?php

namespace App\Livewire;

use App\Exports\LivrosExport;
use App\Models\Livro;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class LivrosTable extends Component
{
    use WithPagination;

    public bool $isAdmin = false;
    public bool $selectPage = false;
    /** @var array<int> */
    public array $selectedLivros = [];

    // Estado dos filtros de pesquisa e intervalo de preco.
    public string $search = '';
    public string $precoMin = '';
    public string $precoMax = '';

    // Estado de visibilidade das colunas da tabela.
    public bool $mostrarISBN = true;
    public bool $mostrarEditora = true;
    public bool $mostrarBibliografia = true;

    // Estado da ordenacao por titulo.
    public string $sortDirection = 'normal';

    protected $queryString = [
        'search' => ['except' => ''],
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
        // Sempre que um filtro principal muda, volta para a pagina 1.
        if (in_array($name, ['search', 'precoMin', 'precoMax'], true)) {
            $this->resetPage();
            $this->selectedLivros = [];
            $this->selectPage = false;
        }
    }

    /**
     * Ciclo de ordenacao por titulo: asc -> desc -> normal.
     */
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

    protected function getLivrosQuery()
    {
        $search = trim($this->search);
        $precoMin = $this->normalizePrice($this->precoMin);
        $precoMax = $this->normalizePrice($this->precoMax);

        $query = Livro::query()
            ->with(['editora', 'autores'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('isbn', 'like', "%{$search}%")
                        ->orWhere('nome', 'like', "%{$search}%")
                        ->orWhereHas('editora', fn ($e) => $e->where('nome', 'like', "%{$search}%"))
                        ->orWhereHas('autores', fn ($a) => $a->where('nome', 'like', "%{$search}%"));
                });
            })
            ->when(!$this->isAdmin && $precoMin !== null, fn ($q) => $q->where('preco', '>=', $precoMin))
            ->when(!$this->isAdmin && $precoMax !== null, fn ($q) => $q->where('preco', '<=', $precoMax));

        if ($this->sortDirection !== 'normal') {
            $query->orderBy('nome', $this->sortDirection);
        } else {
            $query->orderBy('id', 'asc');
        }

        return $query;
    }

    protected function normalizePrice(string $value): ?float
    {
        $normalized = str_replace(',', '.', trim($value));

        if ($normalized === '' || !is_numeric($normalized)) {
            return null;
        }

        return (float) $normalized;
    }

    public function exportarExcel()
    {
        return Excel::download(
            new LivrosExport(
                Livro::query()
                    ->with(['editora', 'autores'])
                    ->orderBy('id', 'asc')
                    ->get()
            ),
            'livros-' . now()->format('Y-m-d-H-i-s') . '.xlsx'
        );
    }

    protected function getCurrentPageIds(): array
    {
        return $this->getLivrosQuery()
            ->paginate(20)
            ->getCollection()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public function updatedSelectPage(bool $value): void
    {
        if (! $this->isAdmin) {
            return;
        }

        $pageIds = $this->getCurrentPageIds();

        if ($pageIds === []) {
            $this->selectPage = false;

            return;
        }

        if (! $value) {
            $this->selectedLivros = array_values(array_diff(array_map('intval', $this->selectedLivros), $pageIds));

            return;
        }

        $this->selectedLivros = array_values(array_unique([
            ...array_map('intval', $this->selectedLivros),
            ...$pageIds,
        ]));
    }

    public function updatedSelectedLivros(): void
    {
        $this->selectedLivros = array_values(array_unique(array_map('intval', $this->selectedLivros)));

        if (! $this->isAdmin) {
            return;
        }

        $pageIds = $this->getCurrentPageIds();
        $this->selectPage = $pageIds !== [] && count(array_diff($pageIds, $this->selectedLivros)) === 0;
    }

    public function eliminarSelecionados(): void
    {
        if (! $this->isAdmin || $this->selectedLivros === []) {
            return;
        }

        $ids = array_map('intval', $this->selectedLivros);

        Livro::query()
            ->whereIn('id', $ids)
            ->get()
            ->each(function (Livro $livro): void {
                $livro->autores()->detach();
                $livro->delete();
            });

        $this->selectedLivros = [];
        $this->selectPage = false;
    }

    public function render()
    {
        $livros = $this->getLivrosQuery()->paginate(20);
        $pageIds = $livros->getCollection()->pluck('id')->map(fn ($id) => (int) $id)->all();

        if ($this->isAdmin) {
            $this->selectPage = $pageIds !== [] && count(array_diff($pageIds, $this->selectedLivros)) === 0;
        }

        // Calcular o número de livros requisitados em simultâneo pelo utilizador autenticado
        $livrosRequisitados = 0;
        if (auth()->check()) {
            $livrosRequisitados = auth()->user()->requisicoes()->whereNull('devolvido_em')->count();
        }

        return view('livewire.livros-table', [
            'livros' => $livros,
            'pageIds' => $pageIds,
            'forRequisicao' => false,
            'livrosRequisitados' => $livrosRequisitados,
        ]);
    }
}
