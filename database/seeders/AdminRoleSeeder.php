<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (User::ADMIN_ROLES as $role) {
            Role::findOrCreate($role, 'web');
        }

        $permissionsByRole = [
            'owner' => ['manage users', 'manage notes', 'view activity log', 'view jobs monitor'],
            'support' => ['manage notes'],
        ];

        foreach ($permissionsByRole as $role => $permissions) {
            Role::findByName($role, 'web')->givePermissionTo(
                array_map(fn (string $permission) => Permission::findOrCreate($permission, 'web'), $permissions)
            );
        }
    }
}
