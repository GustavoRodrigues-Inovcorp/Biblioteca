<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\GoogleBooksService;
use App\Services\LivroImportService;
use Illuminate\Support\Facades\App;
/**
 * Componente Livewire: GooglebooksTable
 *
 * Permite pesquisar livros na API do Google Books e importar para a base de dados local.
 * Mostra popups de confirmação e gere o estado da importação.
 */
class GooglebooksTable extends Component
{
    public $books;
    public $existingIsbns;
    public $q;
    public $success;
    public $mostrarPopupImportar = false;
    public $livroParaImportar = null;

    /**
     * Inicializa o componente com os livros encontrados, ISBNs existentes e estado de pesquisa.
     */
    public function mount($books = null, $existingIsbns = [], $q = '', $success = null)
    {
        $this->books = $books;
        $this->existingIsbns = $existingIsbns;
        $this->q = $q;
        $this->success = $success;
    }

    /**
     * Mostra popup para confirmar importação de um livro.
     */
    public function pedirConfirmacaoImportar($idx)
    {
        if (is_array($this->books) && isset($this->books[$idx])) {
            $this->livroParaImportar = $this->books[$idx];
            $this->mostrarPopupImportar = true;
        }
    }

    /**
     * Cancela o popup de importação.
     */
    public function cancelarImportacao()
    {
        $this->mostrarPopupImportar = false;
        $this->livroParaImportar = null;
    }

    /**
     * Confirma e executa a importação do livro selecionado.
     */
    public function confirmarImportacao()
    {
        // Importar livro diretamente via serviço
        $googleBooksService = App::make(GoogleBooksService::class);
        $livroImportService = App::make(LivroImportService::class);
        $data = $googleBooksService->mapGoogleBookToLivro($this->livroParaImportar);
        $livro = $livroImportService->storeGoogleBook($data);
        $this->mostrarPopupImportar = false;
        $this->livroParaImportar = null;
        session()->flash('success', $livro ? 'Livro importado com sucesso!' : 'Erro ao importar livro.');
        // Atualizar lista de ISBNs existentes para refletir importação
        $this->existingIsbns = \App\Models\Livro::pluck('isbn')->filter(fn($isbn) => strlen($isbn) === 13)->all();
    }

    /**
     * Renderiza a view do componente.
     */
    public function render()
    {
        return view('livewire.googlebooks-table');
    }
}
