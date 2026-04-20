<?php

namespace App\Http\Controllers;

use App\Models\Autor;
use Illuminate\View\View;

class AutorPublicoController extends Controller
{
    public function show(Autor $autor): View
    {
        $autor->load([
            'livros' => fn ($query) => $query
                ->with(['autores'])
                ->orderBy('nome'),
        ]);

        return view('cidadao.autores.show', [
            'autor' => $autor,
        ]);
    }
}
