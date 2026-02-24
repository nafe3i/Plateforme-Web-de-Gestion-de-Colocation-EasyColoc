<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $permissions = [
            'view_statistics',
            'ban_users',
            'disable_users',
            'create_colocation',
            'join_colocation'
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        // Permission::create(['name' => 'viewStatics']);
        // Permission::create(['name' => 'banUsers']);
        // Permission::create(['name' => 'disableUsers']);
        // Permission::create(['name' => 'crateColocation']);
        // Permission::create(['name' => 'joinColocation']);
    }
}
