<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $endDate = fake()->date();

        return [
            'name' => fake()->realText(10),
            'description' => fake()->realText(10),
            'status' => 'active',
            'created_by' => 1,
            'start_date' => fake()->date(max: $endDate),
            'end_date' => $endDate,
        ];
    }
}
