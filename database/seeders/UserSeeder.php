<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Create admin user
        User::factory()->create([
            'name' => 'Site Administrator',
            'email' => 'admin@tasksportal.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        // Create demo user
        User::factory()->create([
            'name' => 'Demo User',
            'email' => 'user@tasksportal.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        // Create 8 more users
        User::factory()->count(8)->create();
    }
}
