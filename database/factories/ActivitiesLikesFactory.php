<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Activity;
use App\Models\ActivitiesLikes;

class ActivitiesLikesFactory extends Factory
{
    protected $model = ActivitiesLikes::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'activity_id' => Activity::inRandomOrder()->first()?->id ?? Activity::factory(),
            'is_fav' => $this->faker->boolean(),
        ];
    }
}
