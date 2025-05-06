<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
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
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            // 'role' => $this->faker->randomElement(['admin', 'mentor', 'mentee']),
            'department_id' => \App\Models\Department::inRandomOrder()->first()?->id ?? null,
            'phone' => $this->faker->phoneNumber,
            'country' => $this->faker->country(),
            'skills' => $this->faker->words(3, true),
            'exp_years' => $this->faker->numberBetween(1, 10),
            'expertise' => json_encode([
                [
                    'name' => $this->faker->jobTitle(),
                    'description' => $this->faker->sentence(),
                    'start_date' => $this->faker->date(),
                    'end_date' => $this->faker->optional()->date(),
                ],
                [
                    'name' => $this->faker->jobTitle(),
                    'description' => $this->faker->sentence(),
                    'start_date' => $this->faker->date(),
                    'end_date' => $this->faker->optional()->date(),
                ]
            ]),

            'education' => json_encode([
                [
                    'name' => $this->faker->word() . ' Degree',
                    'institution' => $this->faker->company(),
                    'start_date' => $this->faker->date(),
                    'end_date' => $this->faker->date(),
                ]
            ]),

            'certificates' => json_encode([
                [
                    'name' => $this->faker->word() . ' Certification',
                    'issued_by' => $this->faker->company(),
                    'issue_date' => $this->faker->date(),
                ],
                [
                    'name' => $this->faker->word() . ' Professional Certificate',
                    'issued_by' => $this->faker->company(),
                    'issue_date' => $this->faker->date(),
                ]
            ]),


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
