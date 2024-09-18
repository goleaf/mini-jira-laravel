<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaskType;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {


        TaskStatus::insert([
                             ['name' => 'In process'],
                             ['name' => 'In testing'],
                             ['name' => 'In production'],
                             ['name' => 'Pause'],
                         ]);

        TaskType::insert([
                             ['name' => 'Task'],
                             ['name' => 'Bug'],
                         ]);


        Task::factory(100)->create([
                                      'task_creator_user_id' => User::factory(),
                                      'assigned_user_id' => User::factory(),
                                      'assigned_tester_user_id' => User::factory(),
                                      'task_type_id' => rand(1,2),
                                      'task_status_id' => rand(1,4),
                                  ]);

        User::create([
                         'name' => 'Site Administrator',
                         'email' => 'admin@tasksportal.com',
                         'password' => bcrypt('password'),
                         'is_admin' => 1
                     ]);

        User::create([
                         'name' => 'Demo User',
                         'email' => 'user@tasksportal.com',
                         'password' => bcrypt('password')
                     ]);


/*
        TaskStatus::create(
            ['title' => 'Task' ],
            ['title' => 'Bug' ],
        );
*/
    }

}
