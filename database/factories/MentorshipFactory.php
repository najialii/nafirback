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
            'mentee_id' => User::factory(),
            'department_id' => Department::factory(),
            'date' => $this->faker->date(),
            'days' => json_encode($this->faker->randomElements(
                ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                $this->faker->numberBetween(1, 5)
            )),
            'available_times' => json_encode([
                'morning' => ['08:00', '10:00'],
                'evening' => ['18:00', '20:00']
            ]),
        ];
    }
}
