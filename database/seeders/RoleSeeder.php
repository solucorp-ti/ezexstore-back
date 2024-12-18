<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['super_admin', 'admin', 'support', 'staff'];
        
        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
    }
}