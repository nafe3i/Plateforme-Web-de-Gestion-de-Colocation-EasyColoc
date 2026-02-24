<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $admin = Role::firstOrCreate(['name' => 'adminGlobal']);
        $user = Role::firstOrCreate(['name' => 'user']);
        // $admin = User::firstOrCreate(['email' => 'admin@easycoloc.com'], [
        //     'name' => 'Admin EasyColoc',
        //     'password' => Hash::make('password123'),
        // ]);
        // if (!$admin->hasRole('admin')) {
        //     $admin->assignRole('admin');
        // }
        $admin->givePermissionTo([
            'view_statistics',
            'ban_users',
            'disable_users',
            'create_colocation',
            'join_colocation'
        ]);

        $user->givePermissionTo([
            'create_colocation',
            'join_colocation'
        ]);
    }
}
