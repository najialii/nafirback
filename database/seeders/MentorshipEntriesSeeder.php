<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MentorshipEntry;

class MentorshipEntriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 mentorship entries
        MentorshipEntry::factory(10)->create();
    }
}