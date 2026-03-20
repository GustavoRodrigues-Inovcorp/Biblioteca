<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin pode criar utilizador admin', function () {
    $admin = User::factory()->admin()->create();

    $response = $this
        ->actingAs($admin)
        ->post(route('admin.admin-users.store'), [
            'name' => 'Novo Admin',
            'email' => 'novo-admin@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

    $response
        ->assertRedirect(route('admin.admin-users.index'))
        ->assertSessionHas('status', 'Utilizador Admin criado com sucesso.');

    $this->assertDatabaseHas('users', [
        'email' => 'novo-admin@example.com',
        'role' => User::ROLE_ADMIN,
    ]);
});

test('utilizador nao admin nao pode criar utilizador admin', function () {
    $cidadao = User::factory()->create();

    $response = $this
        ->actingAs($cidadao)
        ->postJson(route('admin.admin-users.store'), [
            'name' => 'Admin Bloqueado',
            'email' => 'admin-bloqueado@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

    $response->assertForbidden();

    $this->assertDatabaseMissing('users', [
        'email' => 'admin-bloqueado@example.com',
        'role' => User::ROLE_ADMIN,
    ]);
});
