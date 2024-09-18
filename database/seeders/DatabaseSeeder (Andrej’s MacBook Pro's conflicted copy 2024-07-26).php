<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        Task::factory(20)->create([
                                      'taskcreator_id' => User::factory(),
                                      'assigneduser_id' => User::factory()
                                  ]);

        User::create([
                         'name' => 'admin',
                         'email' => 'admin@password.com',
                         'password' => bcrypt('password'),
                         'admin' => 1
                     ]);

        User::create([
                         'name' => 'jane',
                         'email' => 'janedoe@email.com',
                         'password' => bcrypt('password')
                     ]);

    }
}
