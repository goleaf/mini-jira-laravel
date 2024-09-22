<?php

namespace Database\Seeders\demo;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\TaskType;
use App\Models\TaskStatus;
use Faker\Factory as Faker;
use Carbon\Carbon;


class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all()->pluck('id')->toArray();
        $taskTypes = TaskType::all()->pluck('id')->toArray();
        $taskStatuses = TaskStatus::all()->pluck('id')->toArray();
        $faker = Faker::create();

        for ($i = 0; $i < 2000; $i++) {
            $creatorId = $faker->randomElement($users);
            $assignedId = $faker->randomElement(array_diff($users, [$creatorId]));
            $testerId = $faker->randomElement(array_diff($users, [$creatorId, $assignedId]));

            DB::table('tasks')->insert([
                'title' => $faker->sentence($faker->numberBetween(6, 20), true),
                'description' => $faker->paragraph(5, true),
                'task_deadline_date' => $faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
                'task_creator_user_id' => $creatorId,
                'assigned_user_id' => $assignedId,
                'assigned_tester_user_id' => $testerId,
                'task_type_id' => $faker->randomElement($taskTypes),
                'task_status_id' => $faker->randomElement($taskStatuses),
                'created_at' => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
                'updated_at' => now(),
            ]);
        }
    }
}
