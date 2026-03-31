<?php

use App\Http\Controllers\Admin\LivroController as AdminLivroController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\GoogleBooksController;
use App\Http\Controllers\RequisicaoController;
use App\Http\Controllers\LivroPublicoController;
use App\Http\Controllers\ReviewController;
use App\Models\Autor;
use App\Models\Editora;
use App\Models\Livro;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home', [
        'totalLivros' => Livro::count(),
        'totalAutores' => Autor::count(),
        'totalEditoras' => Editora::count(),
    ]);
})->name('home');

Route::get('/livros', function () {
    return view('cidadao.livros.index');
})->name('livros.index');

Route::get('/livros/{livro}', [LivroPublicoController::class, 'show'])->name('livros.show');

Route::get('/autores', function () {
    return view('cidadao.autores.index');
})->name('autores.index');

Route::get('/editoras', function () {
    return view('cidadao.editoras.index');
})->name('editoras.index');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'role:admin,cidadao',
])->group(function (): void {
    Route::get('/requisicoes', [RequisicaoController::class, 'index'])->name('requisicoes.index');
    Route::post('/requisicoes', [RequisicaoController::class, 'store'])->name('requisicoes.store');
    Route::patch('/requisicoes/{requisicao}/devolver', [RequisicaoController::class, 'devolver'])->name('requisicoes.devolver');
    Route::post('/livros/{livro}/review', [ReviewController::class, 'store'])->name('reviews.store');

});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'role:admin',
])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    })->name('index');

    Route::get('/dashboard', function () {
        return view('admin.dashboard', [
            'totalLivros' => Livro::count(),
            'totalAutores' => Autor::count(),
            'totalEditoras' => Editora::count(),
            'totalAdmins' => User::query()->where('role', User::ROLE_ADMIN)->count(),
        ]);
    })->name('dashboard');

    // Google Books API - Importação de livros
    Route::match(['get', 'post'], '/googlebooks', [GoogleBooksController::class, 'index'])->name('googlebooks.index');
    Route::post('/googlebooks/search', [GoogleBooksController::class, 'search'])->name('googlebooks.search');
    Route::post('/googlebooks/import', [GoogleBooksController::class, 'import'])->name('googlebooks.import');

    // Rotas para gestão de livros
    Route::get('/livros', [AdminLivroController::class, 'index'])->name('livros');
    Route::get('/livros/criar', [AdminLivroController::class, 'create'])->name('livros.create');
    Route::post('/livros', [AdminLivroController::class, 'store'])->name('livros.store');
    Route::get('/livros/exportar', [AdminLivroController::class, 'export'])->name('livros.export');
    Route::get('/livros/{livro}', [AdminLivroController::class, 'show'])->name('livros.show');
    Route::get('/livros/{livro}/editar', [AdminLivroController::class, 'edit'])->name('livros.edit');
    Route::put('/livros/{livro}', [AdminLivroController::class, 'update'])->name('livros.update');
    Route::delete('/livros/{livro}', [AdminLivroController::class, 'destroy'])->name('livros.destroy');

    // Rotas para gestão de utilizadores
    Route::get('/utilizadores-admin', [AdminUserController::class, 'index'])->name('admin-users.index');
    Route::get('/utilizadores-admin/criar', [AdminUserController::class, 'create'])->name('admin-users.create');
    Route::post('/utilizadores-admin', [AdminUserController::class, 'store'])->name('admin-users.store');
    
    Route::get('/perfil', function () {
        return view('admin.perfil');
    })->name('perfil');

    // Rotas para gestão de requisições
    Route::get('/requisicoes', function () {
        return view('admin.requisicoes.index');
    })->name('requisicoes');

    // Rotas para gestão de reviews
    Route::get('/reviews', function () {
        return view('admin.reviews.index');
    })->name('reviews');
});

