<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Mentorship;
use App\Models\MentorshipEntry;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MentorshipReq>
 */
class MentorshipReqFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mentorship_id' => Mentorship::factory(), 
            'mentor_id' => User::factory(), 
            'mentee_id' => User::factory(), 
            'message' => $this->faker->sentence(), 
            'status' => $this->faker->randomElement(['pending', 'accepted', 'rejected']), 
        ];
    }
}