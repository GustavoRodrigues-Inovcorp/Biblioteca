<?php

namespace App\Livewire;

use App\Models\Requisicao;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class RequisicaoTable extends Component
{
    use WithPagination;

    public bool $isAdmin = false;
    public ?int $userId = null;
    public string $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function mount(bool $isAdmin = false, ?int $userId = null): void
    {
        $this->isAdmin = $isAdmin;
        $this->userId = $userId;
    }

    public function updating($name)
    {
        if ($name === 'search') {
            $this->resetPage();
        }
    }

    #[Computed]
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

    public function render()
    {
        return view('livewire.requisicao-table');
    }
}
