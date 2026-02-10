<?php

namespace Database\Factories;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->name();
        return [
            'user_id' => User::factory(),
            'name' => $name,
            'normalized_name' => Subject::normalizeName($name),
            'date_of_birth' => fake()->date('Y-m-d', '-1 year'),
            'gender' => fake()->randomElement(['L', 'P']),
            'nik' => fake()->numberBetween(1000, 9999),
        ];
    }
}
