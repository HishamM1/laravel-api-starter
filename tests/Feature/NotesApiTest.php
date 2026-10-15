<?php

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('a user signs in with a password and gets a token', function () {
    User::factory()->create(['email' => 'ada@example.com']);

    $token = $this->postJson('/api/v1/auth/token', ['email' => 'Ada@example.com', 'password' => 'password', 'device_name' => 'phone'])
        ->assertOk()
        ->json('token');

    $this->withToken($token)->getJson('/api/v1/notes')->assertOk();
});

test('a wrong password is rejected', function () {
    User::factory()->create(['email' => 'ada@example.com']);

    $this->postJson('/api/v1/auth/token', ['email' => 'ada@example.com', 'password' => 'nope', 'device_name' => 'phone'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('email');
});

test('revoking the token signs the user out', function () {
    $user = User::factory()->create();
    $token = $user->createToken('phone')->plainTextToken;

    $this->withToken($token)->deleteJson('/api/v1/auth/token')->assertNoContent();

    expect($user->tokens()->count())->toBe(0);
});

test('guests cannot list notes', function () {
    $this->getJson('/api/v1/notes')->assertUnauthorized();
});

test('a user lists only their own notes, filtered by title', function () {
    $user = User::factory()->create();
    Note::factory()->for($user)->create(['title' => 'Groceries']);
    Note::factory()->for($user)->create(['title' => 'Ideas']);
    Note::factory()->create(['title' => 'Groceries elsewhere']);
    Sanctum::actingAs($user);

    $this->getJson('/api/v1/notes?filter[title]=groc')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Groceries');
});

test('creating a note validates and returns it', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->postJson('/api/v1/notes', [])->assertUnprocessable()->assertJsonValidationErrors('title');

    $this->postJson('/api/v1/notes', ['title' => 'Hello', 'body' => 'World'])
        ->assertCreated()
        ->assertJsonPath('title', 'Hello')
        ->assertJsonStructure(['id', 'title', 'body', 'created_at']);
});

test('a user can delete their own note but not someone else\'s', function () {
    $user = User::factory()->create();
    $own = Note::factory()->for($user)->create();
    $other = Note::factory()->create();
    Sanctum::actingAs($user);

    $this->deleteJson("/api/v1/notes/{$other->id}")->assertForbidden();
    $this->deleteJson("/api/v1/notes/{$own->id}")->assertNoContent();

    expect(Note::pluck('id')->all())->toBe([$other->id]);
});
