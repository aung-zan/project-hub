<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => 1,
            'title' => fake()->realText(10),
            'description' => fake()->realText(10),
            'status' => 'todo',
            'priority' => 'low',
            'assigned_to' => 1,
            'created_by' => 1,
            'due_date' => '2026-01-01',
        ];
    }
}
