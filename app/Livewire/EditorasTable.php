<?php

namespace App\Livewire;

use App\Models\Editora;
use Livewire\Component;
use Livewire\WithPagination;

class EditorasTable extends Component
{
    use WithPagination;

    // Estado do filtro de pesquisa por nome.
    public string $search = '';

    // Estado da ordenacao por nome.
    public string $sortDirection = 'normal';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortDirection' => ['except' => 'normal'],
    ];

    public function updating($name)
    {
        // Sempre que a pesquisa muda, volta para a pagina 1.
        if ($name === 'search') {
            $this->resetPage();
        }
    }

    /**
     * Ciclo de ordenacao por nome: asc -> desc -> normal.
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
        // Presets usados pela sidebar para ordenar nome.
        $this->sortDirection = match ($preset) {
            'az'    => 'asc',
            'za'    => 'desc',
            default => 'normal',
        };
        $this->resetPage();
    }

    protected function getEditorasQuery()
    {
        $search = trim($this->search);

        // Query base com contagem e livros associados para evitar N+1 na view.
        $query = Editora::query()
            ->withCount('livros')
            ->with([
                'livros' => fn ($q) => $q->select('livros.id', 'nome', 'imagem_capa', 'editora_id')->orderBy('nome'),
            ])
            ->when($search !== '', function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%");
            });

        if ($this->sortDirection !== 'normal') {
            $query->orderBy('nome', $this->sortDirection);
        } else {
            $query->orderBy('id', 'asc');
        }

        return $query;
    }

    public function render()
    {
        // Paginacao final da listagem.
        $editoras = $this->getEditorasQuery()->paginate(15);

        return view('livewire.editoras-table', ['editoras' => $editoras]);
    }
}
