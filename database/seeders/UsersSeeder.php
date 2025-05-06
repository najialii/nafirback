<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
            ->count(25)
            ->hasDepartment(25)
            ->create();
        $firstUser = User::first();
        $firstUser->password = 'admin';
        $firstUser->email = 'admin@nafir.sd';
        $firstUser->save();

    }
}
