<?php

namespace Database\Seeders\demo;

use App\Models\TaskType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Task',
            'Bug',
            'Feature',
            'Enhancement',
            'Documentation',
            'Refactor',
            'Test',
            'Maintenance',
            'Security',
            'Performance',
            'UI/UX',
            'Integration',
            'Research',
            'Analysis',
            'Planning',
            'Review'
        ];
        foreach ($types as $type) {
            DB::table('task_types')->insert([
                'name' => $type,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
