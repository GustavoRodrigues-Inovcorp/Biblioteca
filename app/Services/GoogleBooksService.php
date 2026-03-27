<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleBooksService
{
    protected $baseUrl = 'https://www.googleapis.com/books/v1/volumes';

    /**
     * Pesquisa livros na Google Books API.
     *
     * @param string $query
     * @param int $maxResults
     * @return array|null
     */
    public function searchBooks(string $query, int $maxResults = 10): ?array
    {
        $response = Http::get($this->baseUrl, [
            'q' => $query,
            'maxResults' => $maxResults,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    /**
     * Mapeia um item da Google Books API para os campos do modelo Livro,
     * incluindo autores e editora (nome).
     *
     * @param array $item
     * @return array
     */
    public function mapGoogleBookToLivro(array $item): array
    {
        $volumeInfo = $item['volumeInfo'] ?? [];

        // ISBN
        $isbn = null;
        if (!empty($volumeInfo['industryIdentifiers'])) {
            foreach ($volumeInfo['industryIdentifiers'] as $identifier) {
                if ($identifier['type'] === 'ISBN_13') {
                    $isbn = $identifier['identifier'];
                    break;
                }
                if ($identifier['type'] === 'ISBN_10' && !$isbn) {
                    $isbn = $identifier['identifier'];
                }
            }
        }

        // Autores
        $autores = $volumeInfo['authors'] ?? [];

        // Editora (nome)
        $editora = $volumeInfo['publisher'] ?? null;

        // Bibliografia (descrição)
        $bibliografia = $volumeInfo['description'] ?? null;

        // Imagem capa
        $imagem_capa = $volumeInfo['imageLinks']['thumbnail'] ?? null;

        // Preço (se disponível)
        $preco = 0.00;
        if (!empty($item['saleInfo']['listPrice']['amount'])) {
            $preco = $item['saleInfo']['listPrice']['amount'];
        }

        return [
            'isbn' => $isbn,
            'nome' => $volumeInfo['title'] ?? null,
            'autores' => $autores,
            'editora' => $editora,
            'bibliografia' => $bibliografia,
            'imagem_capa' => $imagem_capa,
            'preco' => $preco,
        ];
    }
}
