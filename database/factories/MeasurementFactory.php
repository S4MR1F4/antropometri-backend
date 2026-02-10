<?php

namespace Database\Factories;

use App\Models\Measurement;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Measurement>
 */
class MeasurementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject_id' => Subject::factory(),
            'user_id' => User::factory(),
            'measurement_date' => fake()->date(),
            'category' => 'balita',
            'weight' => fake()->randomFloat(2, 2, 20),
            'height' => fake()->randomFloat(2, 40, 110),
            'age_in_months' => fake()->numberBetween(0, 60),
            'age_in_years' => 0,
        ];
    }
}
