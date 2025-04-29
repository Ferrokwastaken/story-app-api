<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()['Spatie\Permission\PermissionRegistrar']->forgetCachedPermissions();

        Permission::create(['name' => 'edit stories']);
        Permission::create(['name' => 'delete stories']);
        Permission::create(['name' => 'manage tags']);
        Permission::create(['name' => 'manage categories']);
        Permission::create(['name' => 'moderate comments']);

        $moderatorRole = Role::create(['name' => 'moderator']);
        $moderatorRole->givePermissionTo(['edit stories', 'delete stories', 'manage tags', 'manage categories', 'moderate comments']);

        $moderatorUser = User::factory()->create([
            'name' => 'Moderator User',
            'email' => 'moderator@example.com',
            'password' => bcrypt('123'),
        ]);
        $moderatorUser->assignRole('moderator');
    }
}
