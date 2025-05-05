<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Department;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mentorship>
 */
class MentorshipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'mentor_id' => User::factory(),
            'department_id' => Department::factory(),
            'date' => $this->faker->date(),
            'av_time' => json_encode([['10:00', '11:00'], ['15:00', '17:00']])
        ];
    }
}
