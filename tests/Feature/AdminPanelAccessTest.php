<?php

use App\Filament\Resources\Notes\NoteResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Database\Seeders\AdminRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(fn () => $this->seed(AdminRoleSeeder::class));

function admin(string $role): User
{
    $admin = User::factory()->create();
    $admin->assignRole($role);
    $admin->saveAppAuthenticationSecret('JBSWY3DPEHPK3PXP');

    return $admin;
}

test('an owner with 2FA can open the panel and every resource', function () {
    $this->actingAs(admin('owner'), 'admin');

    $this->get('/admin')->assertOk();
    $this->get(UserResource::getUrl('index'))->assertOk();
    $this->get(NoteResource::getUrl('index'))->assertOk();
});

test('support can manage notes but not users', function () {
    $this->actingAs(admin('support'), 'admin');

    $this->get(NoteResource::getUrl('index'))->assertOk();
    $this->get(UserResource::getUrl('index'))->assertForbidden();
});

test('an admin without 2FA is sent to set it up', function () {
    $admin = User::factory()->create();
    $admin->assignRole('support');

    $this->actingAs($admin, 'admin')->get('/admin')->assertRedirect('/admin/multi-factor-authentication/set-up');
});

test('the panel rejects IPs outside the allowlist', function () {
    config(['security.admin_ips' => ['10.0.0.0/8']]);

    $this->get('/admin/login')->assertForbidden();
});

test('a user without an admin role is forbidden', function () {
    $this->actingAs(User::factory()->create(), 'admin')->get('/admin')->assertForbidden();
});
