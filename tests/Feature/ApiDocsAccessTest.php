<?php

use App\Models\User;
use Database\Seeders\AdminRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(fn () => $this->seed(AdminRoleSeeder::class));

test('guests cannot read the api document', function () {
    $this->get('/docs/api.json')->assertForbidden();
});

test('admins signed in to the panel can read the api document', function () {
    $admin = User::factory()->create();
    $admin->assignRole('support');

    $this->actingAs($admin, 'admin')->get('/docs/api.json')->assertOk();
});

test('non admin users cannot read the api document', function () {
    $this->actingAs(User::factory()->create(), 'admin')->get('/docs/api.json')->assertForbidden();
});
