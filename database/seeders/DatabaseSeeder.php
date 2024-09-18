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
    public function run()
    {
        $this->seedTaskStatuses();
        $this->seedTaskTypes();
        $this->seedTasks();
        $this->seedUsers();
    }

    /**
     * Seed task statuses.
     */
    private function seedTaskStatuses()
    {
        $taskStatuses = ['In process', 'In testing', 'In production', 'Pause'];
        
        foreach ($taskStatuses as $status) {
            TaskStatus::create(['name' => $status]);
        }
    }

    /**
     * Seed task types.
     */
    private function seedTaskTypes()
    {
        $taskTypes = ['Task', 'Bug'];
        
        foreach ($taskTypes as $type) {
            TaskType::create(['name' => $type]);
        }
    }

    /**
     * Seed tasks.
     */
    private function seedTasks()
    {
        Task::factory(100)->create([
            'task_creator_user_id' => User::factory(),
            'assigned_user_id' => User::factory(),
            'assigned_tester_user_id' => User::factory(),
            'task_type_id' => fn() => rand(1, 2),
            'task_status_id' => fn() => rand(1, 4),
        ]);
    }

    /**
     * Seed users.
     */
    private function seedUsers()
    {
        $users = [
            [
                'name' => 'Site Administrator',
                'email' => 'admin@tasksportal.com',
                'password' => Hash::make('password'),
                'is_admin' => true
            ],
            [
                'name' => 'Demo User',
                'email' => 'user@tasksportal.com',
                'password' => Hash::make('password'),
                'is_admin' => false
            ]
        ];

        User::create($users);
    }
}