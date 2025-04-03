<?php

namespace Database\Seeders;

use App\Models\ActivityParticipant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivitesParticpantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        ActivityParticipant::factory()->count(10)->create();
    }
}
