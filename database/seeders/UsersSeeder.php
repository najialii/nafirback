<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Hash;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\User;
class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        Role::firstOrCreate(['name' => 'user']);
        Role::firstOrCreate(['name' => 'admin']);
    
        $users = User::factory()
            ->count(25)
            ->hasDepartment(25)
            ->create();
    
        foreach ($users as $user) {
            $user->assignRole('user');
        }
    
        $admin = $users->first();
        $admin->password = Hash::make('admin');
        $admin->email = 'admin@nafir.sd';
        $admin->save();
    
        $admin->syncRoles(['admin']);
    }
    
}
