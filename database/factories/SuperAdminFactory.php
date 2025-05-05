<?php

namespace Database\Factories;

use App\Models\SuperAdmin;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SuperAdminFactory extends Factory
{
    protected $model = SuperAdmin::class;

    public function definition(): array
    {
        return [
            'skills' => $this->faker->words(3, true),
            'country' => $this->faker->country(),
            'exp_years' => $this->faker->numberBetween(5, 15),
            'phone' => $this->faker->phoneNumber(),
            'expertise' => json_encode([$this->faker->word(), $this->faker->word()]),
            'education' => json_encode([$this->faker->sentence()]),
            'certificates' => json_encode([$this->faker->sentence()]),
        ];
    }
}
