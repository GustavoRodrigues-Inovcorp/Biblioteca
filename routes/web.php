<?php

use App\Http\Controllers\Admin\LivroController as AdminLivroController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\RequisicaoController;
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

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->get('/dashboard', function () {
    $user = request()->user();

    if ($user?->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('home');
})->name('dashboard');


Route::get('/livros', function () {
    return view('livros');
})->name('livros');

Route::get('/livros/{livro}', [\App\Http\Controllers\LivroPublicoController::class, 'show'])->name('livros.show');

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
    'role:admin,cidadao',
])->group(function (): void {
    Route::get('/requisicoes', [RequisicaoController::class, 'index'])->name('requisicoes.index');
    Route::post('/requisicoes', [RequisicaoController::class, 'store'])->name('requisicoes.store');
    Route::patch('/requisicoes/{requisicao}/devolver', [RequisicaoController::class, 'devolver'])->name('requisicoes.devolver');
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

    Route::get('/livros', [AdminLivroController::class, 'index'])->name('livros');
    Route::get('/livros/criar', [AdminLivroController::class, 'create'])->name('livros.create');
    Route::post('/livros', [AdminLivroController::class, 'store'])->name('livros.store');
    Route::get('/livros/{livro}', [AdminLivroController::class, 'show'])->name('livros.show');
    Route::get('/livros/{livro}/editar', [AdminLivroController::class, 'edit'])->name('livros.edit');
    Route::put('/livros/{livro}', [AdminLivroController::class, 'update'])->name('livros.update');
    Route::delete('/livros/{livro}', [AdminLivroController::class, 'destroy'])->name('livros.destroy');

    Route::get('/utilizadores-admin', [AdminUserController::class, 'index'])->name('admin-users.index');
    Route::get('/utilizadores-admin/criar', [AdminUserController::class, 'create'])->name('admin-users.create');
    Route::post('/utilizadores-admin', [AdminUserController::class, 'store'])->name('admin-users.store');

    Route::get('/perfil', function () {
        return view('admin.perfil');
    })->name('perfil');

    Route::get('/requisicoes', function () {
        return view('admin.requisicoes');
    })->name('requisicoes');
});

