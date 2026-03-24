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

    // Formulário de pesquisa
    public function index()
    {
        return view('admin.googlebooks.index');
    }

    // Processa pesquisa e mostra resultados
    public function search(Request $request)
    {
        $request->validate(['q' => 'required|string']);
        $result = $this->googleBooksService->searchBooks($request->q, 10);
        $books = $result['items'] ?? [];
        return view('admin.googlebooks.results', compact('books'));
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
