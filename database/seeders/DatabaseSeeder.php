<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            demo\UsersGroupsSeeder::class,
            demo\UserSeeder::class,
            demo\TaskStatusSeeder::class,
            demo\TaskTypeSeeder::class,
            demo\TaskSeeder::class,
            demo\CommentSeeder::class,
        ]);
    }
}
