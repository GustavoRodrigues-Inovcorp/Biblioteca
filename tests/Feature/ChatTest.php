<?php

use App\Models\ChatConversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('utilizador pode enviar mensagem direta', function () {
    $sender = User::factory()->create(['name' => 'Alice']);
    $receiver = User::factory()->create(['name' => 'Bruno']);

    $conversation = ChatConversation::query()->create([
        'type' => ChatConversation::TYPE_DIRECT,
        'name' => 'Bruno',
        'created_by_id' => $sender->id,
    ]);

    $conversation->participants()->sync([$sender->id, $receiver->id]);

    Livewire::actingAs($sender)
        ->test('chat.chat-page')
        ->set('selectedConversationId', $conversation->id)
        ->set('messageBody', 'Olá Bruno')
        ->call('sendMessage')
        ->assertStatus(200);

    $this->assertDatabaseHas('chat_messages', [
        'conversation_id' => $conversation->id,
        'user_id' => $sender->id,
        'body' => 'Olá Bruno',
    ]);
});

test('apenas admin pode criar salas', function () {
    $cidadao = User::factory()->create();

    Livewire::actingAs($cidadao)
        ->test('chat.chat-page')
        ->call('toggleRoomForm')
        ->assertForbidden();
});

test('admin pode criar sala com participantes', function () {
    $admin = User::factory()->admin()->create();
    $maria = User::factory()->create(['name' => 'Maria']);
    $joao = User::factory()->create(['name' => 'Joao']);

    Livewire::actingAs($admin)
        ->test('chat.chat-page')
        ->set('roomName', 'Sala de Suporte')
        ->set('roomAvatar', 'https://example.com/avatar.png')
        ->set('roomParticipantIds', [$maria->id, $joao->id])
        ->call('createRoom')
        ->assertStatus(200);

    $this->assertDatabaseHas('chat_conversations', [
        'type' => ChatConversation::TYPE_ROOM,
        'name' => 'Sala de Suporte',
        'created_by_id' => $admin->id,
    ]);

    $conversation = ChatConversation::query()->where('name', 'Sala de Suporte')->firstOrFail();

    expect($conversation->participants()->count())->toBe(3);
});