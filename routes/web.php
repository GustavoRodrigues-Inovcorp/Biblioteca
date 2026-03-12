<?php

use App\Models\Autor;
use App\Models\Editora;
use App\Models\Livro;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home', [
        'totalLivros' => Livro::count(),
        'totalAutores' => Autor::count(),
        'totalEditoras' => Editora::count(),
    ]);
})->name('home');

Route::get('/dashboard', function () {
    return redirect()->route('home');
})->name('dashboard');

Route::get('/livros', function () {
    return view('livros');
})->name('livros');

Route::get('/autores', function () {
    return view('autores');
})->name('autores');

Route::get('/editoras', function () {
    return view('editoras');
})->name('editoras');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
]);

