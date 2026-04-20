<?php

use App\Models\ActivityLog;
use App\Models\Livro;
use App\Models\Requisicao;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

// TESTE 1: Criação de requisição de livro
// Objetivo: garantir que um utilizador cria uma requisição com dados corretos.
test('utilizador pode criar requisicao de livro', function () {
    // Preparar cenário e isolar efeitos externos.
    Mail::fake();

    Carbon::setTestNow('2026-04-15 10:00:00');

    // Criar utilizador autenticado.
    $user = User::factory()->create();

    // Criar livro disponível para requisição.
    $livro = Livro::query()->create([
        'isbn' => '9789720049576',
        'nome' => 'Clean Architecture',
        'preco' => 24.90,
    ]);

    // Simular submissão de requisição pelo utilizador.
    $response = $this
        ->actingAs($user)
        ->post(route('requisicoes.store'), [
            'livro_id' => $livro->id,
        ]);

    // Validar resposta HTTP e mensagem de sucesso.
    $response->assertStatus(302);
    $response->assertSessionHas('status', 'Requisição criada com sucesso.');

    // Validar dados persistidos da requisição.
    $this->assertDatabaseHas('requisicoes', [
        'numero' => 1,
        'user_id' => $user->id,
        'livro_id' => $livro->id,
        'requisitado_em' => '2026-04-15 10:00:00',
        'fim_previsto_em' => '2026-04-20 10:00:00',
        'devolvido_em' => null,
    ]);

    $requisicao = Requisicao::query()->firstOrFail();

    // Validar registo de log da ação.
    $this->assertDatabaseHas('logs', [
        'user_id' => $user->id,
        'modulo' => 'Requisicao',
        'objeto_id' => (string) $requisicao->id,
    ]);

    $log = ActivityLog::where('modulo', 'Requisicao')->first();
    expect($log)->not->toBeNull();
    expect($log->alteracao)->toContain('criado');

    Carbon::setTestNow();
});

// TESTE 2: Validação de requisição com livro inválido
// Objetivo: impedir criação quando o livro não existe.
test('requisicao nao pode ser criada sem livro valido', function () {
    // Preparar utilizador autenticado.
    Mail::fake();

    $user = User::factory()->create();

    // Tentar criar requisição com livro_id inexistente.
    $response = $this
        ->actingAs($user)
        ->from(route('requisicoes.index'))
        ->post(route('requisicoes.store'), [
            'livro_id' => 999999,
        ]);

    // Validar erro de validação e ausência de registos criados.
    $response->assertRedirect(route('requisicoes.index'));
    $response->assertSessionHasErrors(['livro_id']);

    $this->assertDatabaseCount('requisicoes', 0);
});

// TESTE 3: Devolução de livro
// Objetivo: confirmar atualização do estado de uma requisição ativa.
test('utilizador pode devolver livro de requisicao ativa', function () {
    // Criar utilizador e uma requisição ativa.
    Mail::fake();

    $user = User::factory()->create();

    $livro = Livro::query()->create([
        'isbn' => '9789720049578',
        'nome' => 'Refactoring',
        'preco' => 31.50,
    ]);

    $requisicao = Requisicao::query()->create([
        'numero' => 1,
        'user_id' => $user->id,
        'livro_id' => $livro->id,
        'requisitado_em' => now()->subDays(2),
        'fim_previsto_em' => now()->addDays(3),
        'devolvido_em' => null,
    ]);

    // Simular pedido de devolução.
    $response = $this
        ->actingAs($user)
        ->patch(route('requisicoes.devolver', $requisicao));

    // Validar resposta e atualização do campo devolvido_em.
    $response->assertRedirect(route('requisicoes.index'));
    $response->assertSessionHas('status', 'Livro devolvido com sucesso.');

    $requisicao->refresh();
    expect($requisicao->devolvido_em)->not->toBeNull();

    // Validar log da devolução.
    $this->assertDatabaseHas('logs', [
        'user_id' => $user->id,
        'modulo' => 'Requisicao',
        'objeto_id' => (string) $requisicao->id,
    ]);
});

// TESTE 4: Listagem de requisições por utilizador
// Objetivo: garantir que cada utilizador vê apenas as suas requisições.
test('utilizador ve apenas as suas requisicoes', function () {
    // Criar dois utilizadores e respetivos livros/requisições.
    $user = User::factory()->create(['name' => 'Utilizador Um']);
    $outroUser = User::factory()->create(['name' => 'Utilizador Dois']);

    $livroDoUser = Livro::query()->create([
        'isbn' => '9789720049579',
        'nome' => 'Livro do Utilizador Um',
        'preco' => 18.00,
    ]);

    $livroDoOutroUser = Livro::query()->create([
        'isbn' => '9789720049580',
        'nome' => 'Livro do Utilizador Dois',
        'preco' => 19.00,
    ]);

    Requisicao::query()->create([
        'numero' => 1,
        'user_id' => $user->id,
        'livro_id' => $livroDoUser->id,
        'requisitado_em' => now()->subDays(1),
        'fim_previsto_em' => now()->addDays(4),
        'devolvido_em' => null,
    ]);

    Requisicao::query()->create([
        'numero' => 2,
        'user_id' => $outroUser->id,
        'livro_id' => $livroDoOutroUser->id,
        'requisitado_em' => now()->subDays(1),
        'fim_previsto_em' => now()->addDays(4),
        'devolvido_em' => null,
    ]);

    // Pedir a listagem autenticado como o primeiro utilizador.
    $response = $this
        ->actingAs($user)
        ->get(route('requisicoes.index'));

    // Validar que só vê os próprios dados.
    $response->assertOk();
    $response->assertSee('Livro do Utilizador Um');
    $response->assertDontSee('Livro do Utilizador Dois');
});

// TESTE 5: Regra de stock
// Objetivo: impedir requisição quando o livro não tem stock.
test('nao e possivel requisitar livro sem stock disponivel', function () {
    // Criar utilizador e livro sem stock.
    Mail::fake();

    $user = User::factory()->create();

    $livroSemStock = Livro::query()->create([
        'isbn' => '9789720049581',
        'nome' => 'Livro Sem Stock',
        'preco' => 21.00,
        'stock' => 0,
    ]);

    // Tentar requisitar livro indisponível.
    $response = $this
        ->actingAs($user)
        ->from(route('requisicoes.index'))
        ->post(route('requisicoes.store'), [
            'livro_id' => $livroSemStock->id,
        ]);

    // Validar bloqueio com mensagem de erro adequada.
    $response->assertRedirect(route('requisicoes.index'));
    $response->assertSessionHasErrors(['livro_id']);
    $response->assertSessionHasErrors([
        'livro_id' => 'Este livro não tem stock disponível para requisição.',
    ]);

    // Confirmar que nenhuma requisição foi criada.
    $this->assertDatabaseMissing('requisicoes', [
        'livro_id' => $livroSemStock->id,
        'user_id' => $user->id,
    ]);
});
