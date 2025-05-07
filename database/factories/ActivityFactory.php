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
            'date' => $this->faker->date(),
            'eventsSchedule' => json_encode([
                'day' => $this->faker->dayOfWeek(),
                'activities' => $this->faker->words(3),
            ]),
            'time' => $this->faker->time(),
            'type' => $this->faker->randomElement(['seminar', 'workshop', 'conference']),
            'user_id' => \App\Models\User::factory(),
            // 'participants' => \App\Models\User::factory(),
            'benifites' => $this->faker->text(),
            'link'=> $this->faker->url,
            'pass_code'=> $this->faker->numberBetween(7,8),
            'instructions' => $this->faker->paragraph(10),
            'created_at' => now(),
            'updated_at' => now(),
            
        ];
    }
}
