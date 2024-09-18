<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        $faker = $this->faker;
        return [
            'title' => $faker->sentence(),
            'description' => $faker->paragraph(5),
            'task_deadline_date' => $faker->dateTimeBetween('now', '1 year'),
        ];
    }
}
