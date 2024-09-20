<?php

namespace Database\Seeders;


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
        $users = User::all();
        $taskTypes = TaskType::all();
        $taskStatuses = TaskStatus::all();
        $faker = Faker::create();

        for ($i = 0; $i < 500; $i++) {
            DB::table('tasks')->insert([
                'title' => $faker->sentence($faker->numberBetween(6, 20), true),
                'description' => $faker->paragraph(3, true),
                'task_deadline_date' => $faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
                'task_creator_user_id' => $users->random()->id,
                'assigned_user_id' => $users->random()->id,
                'assigned_tester_user_id' => $users->random()->id,
                'task_type_id' => $taskTypes->random()->id,
                'task_status_id' => $taskStatuses->random()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
