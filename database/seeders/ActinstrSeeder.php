<?php

namespace Database\Seeders;

use App\Models\Activity_instructor;
use Database\Factories\Activity_instructorFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActinstrSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Activity_instructor::factory(10)->create();
    }
}
