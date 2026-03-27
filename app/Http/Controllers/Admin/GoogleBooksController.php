<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GoogleBooksService;
use App\Services\LivroImportService;

class GoogleBooksController extends Controller
{
    protected $googleBooksService;
    protected $livroImportService;

    public function __construct(GoogleBooksService $googleBooksService, LivroImportService $livroImportService)
    {
        $this->googleBooksService = $googleBooksService;
        $this->livroImportService = $livroImportService;
    }


    // Formulário de pesquisa e resultados
    public function index(Request $request)
    {
        $books = null;
        $existingIsbns = [];
        $q = $request->input('q');
        if ($q) {
            $result = $this->googleBooksService->searchBooks($q, 10);
            $books = $result['items'] ?? [];
            $existingIsbns = \App\Models\Livro::pluck('isbn')->filter(fn($isbn) => strlen($isbn) === 13)->all();
        }
        return view('admin.googlebooks.index', compact('books', 'existingIsbns', 'q'));
    }

    // Redirecionar POST para index para manter tudo na mesma view
    public function search(Request $request)
    {
        $request->validate(['q' => 'required|string']);
        return redirect()->route('admin.googlebooks.index', ['q' => $request->q]);
    }

    // Importa livro selecionado
    public function import(Request $request)
    {
        $request->validate(['book' => 'required']);
        // book chega como string JSON, decodifica para array
        $book = json_decode($request->input('book'), true);
        if (!$book) {
            return redirect()->route('admin.googlebooks.index')->with('error', 'Erro ao importar livro: dados inválidos.');
        }
        $data = $this->googleBooksService->mapGoogleBookToLivro($book);
        $livro = $this->livroImportService->storeGoogleBook($data);
        return redirect()->route('admin.googlebooks.index')->with('success', 'Livro importado com sucesso!');
    }
}
