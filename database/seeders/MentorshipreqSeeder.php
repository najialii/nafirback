<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\MentorshipReq;
use Illuminate\Database\Seeder;

class MentorshipreqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        MentorshipReq::factory()->count(10)->create();
    }
}
