<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Mentorship;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MentorshipEntry>
 */
class MentorshipEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => $this->faker->dateTimeBetween('now', '+1 year'), 
            'duration' => $this->faker->numberBetween(30, 120), 
            'bookedBy' => User::factory(), 
            'mentorship_id' =>Mentorship::factory(),
        ];
    }
}