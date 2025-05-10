<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $mentor = Role::firstOrCreate(['name' => 'mentor']);
        $user = Role::firstOrCreate(['name' => 'user']);

        // Give super admin all permissions
        $superAdmin->syncPermissions(Permission::all());
    }
}
