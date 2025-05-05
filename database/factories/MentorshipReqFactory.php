<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mentorshipreq>
 */
use App\Models\Mentorship;
class MentorshipreqFactory extends Factory
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
            'mentor_id' => $this->faker->randomDigitNotNull(),
            'mentee_id' => $this->faker->randomDigitNotNull(),
            'selecteday' => $this->faker->date(),
            'selectedtime' => $this->faker->time(),
            'message' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['pending', 'accepted', 'rejected']),
        ];
    }
}
