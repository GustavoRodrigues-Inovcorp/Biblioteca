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
    $this->assertDatabaseHas('chat_conversation_user', [
        'chat_conversation_id' => $conversation->id,
        'user_id' => $admin->id,
        'role' => 'admin',
    ]);
});

test('admin pode convidar, promover e expulsar membros de grupo', function () {
    $admin = User::factory()->admin()->create();
    $maria = User::factory()->create(['name' => 'Maria']);
    $joao = User::factory()->create(['name' => 'Joao']);
    $carlos = User::factory()->create(['name' => 'Carlos']);

    $conversation = ChatConversation::query()->create([
        'type' => ChatConversation::TYPE_DIRECT,
        'name' => 'Grupo de Teste',
        'created_by_id' => $admin->id,
    ]);

    $conversation->participants()->sync([
        $admin->id => ['role' => 'admin'],
        $maria->id => ['role' => 'member'],
        $joao->id => ['role' => 'member'],
    ]);

    Livewire::actingAs($admin)
        ->test('chat.chat-page')
        ->set('selectedConversationId', $conversation->id)
        ->set('roomInviteIds', [$carlos->id])
        ->call('inviteSelectedUsers')
        ->assertStatus(200)
        ->call('promoteConversationParticipant', $maria->id)
        ->assertStatus(200)
        ->call('removeConversationParticipant', $joao->id)
        ->assertStatus(200);

    $this->assertDatabaseHas('chat_conversation_user', [
        'chat_conversation_id' => $conversation->id,
        'user_id' => $carlos->id,
        'role' => 'member',
    ]);

    $this->assertDatabaseHas('chat_conversation_user', [
        'chat_conversation_id' => $conversation->id,
        'user_id' => $maria->id,
        'role' => 'admin',
    ]);

    $this->assertDatabaseMissing('chat_conversation_user', [
        'chat_conversation_id' => $conversation->id,
        'user_id' => $joao->id,
    ]);
});

test('membro normal nao pode gerir membros de grupo', function () {
    $creator = User::factory()->admin()->create();
    $member = User::factory()->create();
    $other = User::factory()->create();

    $conversation = ChatConversation::query()->create([
        'type' => ChatConversation::TYPE_DIRECT,
        'name' => 'Grupo de Acesso',
        'created_by_id' => $creator->id,
    ]);

    $conversation->participants()->sync([
        $creator->id => ['role' => 'admin'],
        $member->id => ['role' => 'member'],
    ]);

    Livewire::actingAs($member)
        ->test('chat.chat-page')
        ->set('selectedConversationId', $conversation->id)
        ->call('promoteConversationParticipant', $other->id)
        ->assertForbidden();
});

test('utilizador pode apagar conversa direta um para um', function () {
    $alice = User::factory()->create(['name' => 'Alice']);
    $bruno = User::factory()->create(['name' => 'Bruno']);

    $conversation = ChatConversation::query()->create([
        'type' => ChatConversation::TYPE_DIRECT,
        'name' => 'Alice e Bruno',
        'created_by_id' => $alice->id,
    ]);

    $conversation->participants()->sync([
        $alice->id => ['role' => 'admin'],
        $bruno->id => ['role' => 'member'],
    ]);

    Livewire::actingAs($alice)
        ->test('chat.chat-page')
        ->set('selectedConversationId', $conversation->id)
        ->call('deleteDirectConversation')
        ->assertStatus(200);

    $this->assertDatabaseMissing('chat_conversations', [
        'id' => $conversation->id,
    ]);
});

test('nao permite apagar conversa direta de grupo', function () {
    $owner = User::factory()->create();
    $memberA = User::factory()->create();
    $memberB = User::factory()->create();

    $conversation = ChatConversation::query()->create([
        'type' => ChatConversation::TYPE_DIRECT,
        'name' => 'Grupo 3 pessoas',
        'created_by_id' => $owner->id,
    ]);

    $conversation->participants()->sync([
        $owner->id => ['role' => 'admin'],
        $memberA->id => ['role' => 'member'],
        $memberB->id => ['role' => 'member'],
    ]);

    Livewire::actingAs($owner)
        ->test('chat.chat-page')
        ->set('selectedConversationId', $conversation->id)
        ->call('deleteDirectConversation')
        ->assertForbidden();
});

test('criador nao pode sair de uma sala', function () {
    $creator = User::factory()->admin()->create();
    $member = User::factory()->create();

    $conversation = ChatConversation::query()->create([
        'type' => ChatConversation::TYPE_ROOM,
        'name' => 'Sala de Apoio',
        'created_by_id' => $creator->id,
    ]);

    $conversation->participants()->sync([
        $creator->id => ['role' => 'admin'],
        $member->id => ['role' => 'member'],
    ]);

    Livewire::actingAs($creator)
        ->test('chat.chat-page')
        ->set('selectedConversationId', $conversation->id)
        ->call('leaveConversation')
        ->assertForbidden();
});

test('utilizador pode sair de uma sala sem apagar a conversa', function () {
    $creator = User::factory()->admin()->create();
    $member = User::factory()->create();

    $conversation = ChatConversation::query()->create([
        'type' => ChatConversation::TYPE_ROOM,
        'name' => 'Sala de Apoio',
        'created_by_id' => $creator->id,
    ]);

    $conversation->participants()->sync([
        $creator->id => ['role' => 'admin'],
        $member->id => ['role' => 'member'],
    ]);

    Livewire::actingAs($member)
        ->test('chat.chat-page')
        ->set('selectedConversationId', $conversation->id)
        ->call('leaveConversation')
        ->assertStatus(200);

    $this->assertDatabaseMissing('chat_conversation_user', [
        'chat_conversation_id' => $conversation->id,
        'user_id' => $member->id,
    ]);

    $this->assertDatabaseHas('chat_conversations', [
        'id' => $conversation->id,
    ]);
});

test('criador pode apagar um grupo', function () {
    $creator = User::factory()->create();
    $memberA = User::factory()->create();
    $memberB = User::factory()->create();

    $conversation = ChatConversation::query()->create([
        'type' => ChatConversation::TYPE_DIRECT,
        'name' => 'Grupo de Remoção',
        'created_by_id' => $creator->id,
    ]);

    $conversation->participants()->sync([
        $creator->id => ['role' => 'admin'],
        $memberA->id => ['role' => 'admin'],
        $memberB->id => ['role' => 'member'],
    ]);

    Livewire::actingAs($creator)
        ->test('chat.chat-page')
        ->set('selectedConversationId', $conversation->id)
        ->call('deleteManagedConversation')
        ->assertStatus(200);

    $this->assertDatabaseMissing('chat_conversation_user', [
        'chat_conversation_id' => $conversation->id,
        'user_id' => $creator->id,
    ]);

    $this->assertDatabaseMissing('chat_conversations', [
        'id' => $conversation->id,
    ]);
});