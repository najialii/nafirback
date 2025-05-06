<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActivitiesLikes;

class activityLikeSeeder extends Seeder
{
    public function run(): void
    {
        ActivitiesLikes::factory()->count(50)->create();
    }
}
