<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'profile_pic' => $this->faker->imageUrl(300, 300, 'people', true, 'User'),
            'phone' => $this->faker->phoneNumber(),
            'title' => $this->faker->jobTitle(), 
            'skills' => json_encode($this->faker->words(5)), 
            'education' => json_encode([
                [
                    'university' => $this->faker->company(),
                    'certificate' => $this->faker->word() . ' Degree',
                    'degree' => $this->faker->word(),
                    'period' => [
                        'start' => $this->faker->year(),
                        'end' => $this->faker->year(),
                    ],
                    'description' => $this->faker->sentence(),
                ],
            ]), 
            'experience' => json_encode([
                [
                    'title' => $this->faker->jobTitle(),
                    'company' => $this->faker->company(),
                    'period' => [
                        'start' => $this->faker->year(),
                        'end' => $this->faker->optional()->year(),
                    ],
                    'description' => $this->faker->sentence(),
                ],
            ]), 
            'location' => json_encode([
                'country' => $this->faker->country(),
                'city' => $this->faker->city(),
            ]), 
            'cv_file' => $this->faker->url(), 
            'targeted_locations' => json_encode($this->faker->words(3)), 
            'targeted_industries' => json_encode($this->faker->words(3)), 
            'targeted_titles' => json_encode($this->faker->words(3)), 
            'career_tasks' => $this->faker->paragraph(), 
            'completion_percentage' => $this->faker->numberBetween(0, 100), 
            'department_id' => \App\Models\Department::inRandomOrder()->first()?->id ?? null,
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}