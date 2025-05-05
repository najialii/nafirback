<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use App\Models\Department;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class BlogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'img' => UploadedFile::fake()->image('blog.jpg'),
            'author_id' => User::factory(),
            'title' => $this->faker->sentence(),
            'department_id' => Department::factory(),
            'content' => $this->faker->paragraphs(3, true),
            'slug' => $this->faker->word(),
            'featured' => $this->faker->boolean(),
        ];
    }
}
