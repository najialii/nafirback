<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
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
            'description' => $this->faker->paragraph(),
            'department_id' => \App\Models\Department::factory(),
            'location' => $this->faker->city(),
            'eventsSchedule' => json_encode([
                'day' => $this->faker->dayOfWeek(),
                'activities' => $this->faker->words(3),
            ]),
            'date' => $this->faker->date(),
            'time' => $this->faker->time(),
            'type' => $this->faker->randomElement(['seminar', 'workshop', 'conference']),
            'user_id' => \App\Models\User::factory(),
            'participants' => $this->faker->numberBetween(10, 200),
            'benifites' => $this->faker->text(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
