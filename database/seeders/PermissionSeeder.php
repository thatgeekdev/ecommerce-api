<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Cria a permissão
        Permission::firstOrCreate([
            'name' => 'manage-products',
            'guard_name' => 'api',
        ]);

        // Cria a role
        $role = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'api',
        ]);

        // Associa permissão à role
        $role->givePermissionTo('manage-products');

        // Usuário de teste
        $user = User::firstOrCreate([
            'email' => 'josejaimematsimbe@gmail.com'
        ], [
            'name' => 'super admin',
            'password' => bcrypt('password'),
        ]);

        // Associa role ao usuário corretamente no model_has_role (spatie)
        $user->assignRole('admin');
        $user->givePermissionTo('manage-products');
        
    }
}