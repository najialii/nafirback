<?php
namespace Database\Factories;

use App\Helpers\ImageHelper;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'profile_pic' => ImageHelper::generateImageUrl('generic'),
            'phone' => $this->faker->phoneNumber(),
            'title' => $this->faker->jobTitle(),
            'skills' => $this->faker->words(5),
            'education' => [
                [
                    'university' => $this->faker->company(),
                    'certificate' => $this->faker->word() . ' Degree',
                    'degree' => $this->faker->word(),
                    'startDate' => $this->faker->year(),
                    'endDate' => $this->faker->year(),
                    'description' => $this->faker->sentence(),
                ],
            ],
            'experience' => [
                [
                    'title' => $this->faker->jobTitle(),
                    'company' => $this->faker->company(),
                    'startDate' => $this->faker->year(),
                    'endDate' => $this->faker->optional()->year(),
                    'description' => $this->faker->sentence(),
                ],
            ],
            'location' => [
                'country' => $this->faker->country(),
                'city' => $this->faker->city(),
            ],
            'cv_file' => $this->faker->url(),
            'targeted_locations' => $this->faker->words(3),
            'targeted_industries' => $this->faker->words(3),
            'targeted_titles' => $this->faker->words(3),
            'career_tasks' => $this->faker->paragraph(),
            'completion_percentage' => $this->faker->numberBetween(0, 100),
            'department_id' => \App\Models\Department::inRandomOrder()->first()?->id ?? null,
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
