<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SuperAdmin;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => 'super_admin']);

        User::factory()
            ->count(10)
            ->create()
            ->each(function ($user) use ($role) {
                $user->assignRole($role); // تعيين الرول
                SuperAdmin::factory()->create(['user_id' => $user->id]);
            });
    }
}
