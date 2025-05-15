<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Department;
use App\Models\User;
use App\Models\MentorshipEntry;
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
            'description' => $this->faker->sentence(),
            'benefits' => $this->faker->sentences(3),
// 'benefits' => json_encode($this->faker->sentences(3)),

            'start_date' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'end_date' => $this->faker->dateTimeBetween('+1 year', '+2 years')->format('Y-m-d'),
        ];  
    }

    /**
     * Configure the factory to create related entries.
     *
     * @return $this
     */
    // public function configure(): MentorshipFactory
    // {
    //     return $this->afterCreating(function ($mentorship) {
    //         MentorshipEntry::factory(3)->create([
    //             'date' => $this->faker->dateTimeBetween('now', '+1 year'), // Full datetime for entries
    //             'duration' => $this->faker->numberBetween(30, 120), // Duration in minutes
    //             'bookedBy' => User::factory(), // User who booked
    //             'mentor_id' => $mentorship->mentor_id, // Mentor ID from mentorship
    //         ]);
    //     });
    // }
}