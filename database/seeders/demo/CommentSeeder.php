<?php

namespace Database\Seeders\demo;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\User;
use App\Models\Task;
use Faker\Factory as Faker;

class CommentSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $users = User::all();
        $tasks = Task::all();

        // Create 100 random comments
        for ($i = 0; $i < 100; $i++) {
            $comment = Comment::create([
                'user_id' => $users->random()->id,
                'task_id' => $tasks->random()->id,
                'body' => $faker->paragraph,
                'created_at' => $faker->dateTimeBetween('-1 month', 'now'),
                'updated_at' => $faker->dateTimeBetween('-1 month', 'now'),
            ]);

            // 30% chance of being a reply to another comment
            if (rand(1, 100) <= 30) {
                $parentComment = Comment::where('task_id', $comment->task_id)
                    ->where('id', '!=', $comment->id)
                    ->inRandomOrder()
                    ->first();

                if ($parentComment) {
                    $comment->parent_id = $parentComment->id;
                    $comment->save();
                }
            }
        }
    }
}
