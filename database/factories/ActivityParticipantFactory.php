<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\ActivityReq;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivitiesParticipant>
 */
class ActivityParticipantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'user_id' => User::factory(),
            'activityreq_id' => ActivityReq::factory(),
        ];
    }
}
