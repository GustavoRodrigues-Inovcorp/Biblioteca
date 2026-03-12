<?php

namespace App\Livewire;

use App\Exports\LivrosExport;
use App\Models\Livro;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\Component;
use Livewire\WithPagination;

class LivrosTable extends Component
{
    use WithPagination;

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

    public function updating($name)
    {
        // Sempre que um filtro principal muda, volta para a pagina 1.
        if (in_array($name, ['search', 'precoMin', 'precoMax'], true)) {
            $this->resetPage();
        }
    }

    /**
     * Ciclo de ordenacao por titulo: asc -> desc -> normal.
     */
    public function sortBy(): void
    {
        // Ciclo: asc -> desc -> normal -> asc
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
        // Presets usados pela sidebar para ordenar título.
        $this->sortDirection = match ($preset) {
            'az'     => 'asc',
            'za'     => 'desc',
            default  => 'normal',
        };
        $this->resetPage();
    }

    protected function getLivrosQuery()
    {
        $search = trim($this->search);
        $precoMin = $this->normalizePrice($this->precoMin);
        $precoMax = $this->normalizePrice($this->precoMax);

        // Query base com relacionamentos para evitar N+1 na view.
        $query = Livro::query()
            ->with(['editora', 'autores'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('isbn', 'like', "%{$search}%")
                        ->orWhere('nome', 'like', "%{$search}%")
                        ->orWhereHas('editora', fn($e) => $e->where('nome', 'like', "%{$search}%"))
                        ->orWhereHas('autores', fn($a) => $a->where('nome', 'like', "%{$search}%"));
                });
            })
            ->when($precoMin !== null, fn($q) => $q->where('preco', '>=', $precoMin))
            ->when($precoMax !== null, fn($q) => $q->where('preco', '<=', $precoMax));

        if ($this->sortDirection !== 'normal') {
            $query->orderBy('nome', $this->sortDirection);
        } else {
            $query->orderBy('id', 'asc');
        }

        return $query;
    }

    protected function normalizePrice(string $value): ?float
    {
        // Aceita virgula ou ponto e devolve null se valor nao for numerico.
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


    public function render()
    {
        // Paginacao final da listagem.
        $livros = $this->getLivrosQuery()->paginate(20);

        return view('livewire.livros-table', ['livros' => $livros]);
    }
}