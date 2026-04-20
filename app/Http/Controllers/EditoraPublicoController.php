<?php

namespace App\Http\Controllers;

use App\Models\Editora;
use Illuminate\View\View;

class EditoraPublicoController extends Controller
{
    public function show(Editora $editora): View
    {
        $editora->load([
            'livros' => fn ($query) => $query
                ->with(['autores'])
                ->orderBy('nome'),
        ]);

        return view('cidadao.editoras.show', [
            'editora' => $editora,
        ]);
    }
}
