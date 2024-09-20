<?php

namespace Database\Seeders;

use App\Models\TaskStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'New',
            'In process',
            'In testing',
            'Ready for review',
            'In production',
            'Completed',
            'Pause',
            'Cancelled'
        ];
        foreach ($statuses as $status) {
            DB::table('task_statuses')->insert([
                'name' => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
