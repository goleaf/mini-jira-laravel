<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaskType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TaskStatusSeeder::class,
            TaskTypeSeeder::class,
            TaskSeeder::class,
            UserSeeder::class,
        ]);
    }
}

class TaskStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ['In process', 'In testing', 'In production', 'Pause'];
        foreach ($statuses as $status) {
            TaskStatus::create(['name' => $status]);
        }
    }
}

class TaskTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['Task', 'Bug'];
        foreach ($types as $type) {
            TaskType::create(['name' => $type]);
        }
    }
}

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        Task::factory(100)->create([
            'task_creator_user_id' => fn() => User::factory()->create()->id,
            'assigned_user_id' => fn() => User::factory()->create()->id,
            'assigned_tester_user_id' => fn() => User::factory()->create()->id,
            'task_type_id' => fn() => TaskType::inRandomOrder()->first()->id,
            'task_status_id' => fn() => TaskStatus::inRandomOrder()->first()->id,
        ]);
    }
}

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Site Administrator',
                'email' => 'admin@tasksportal.com',
                'password' => 'password',
                'is_admin' => true
            ],
            [
                'name' => 'Demo User',
                'email' => 'user@tasksportal.com',
                'password' => 'password',
                'is_admin' => false
            ]
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make($user['password']),
                'is_admin' => $user['is_admin']
            ]);
        }
    }
}
