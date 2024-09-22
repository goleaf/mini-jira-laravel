<?php

namespace Database\Seeders\demo;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserGroup;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Get all user groups
        $userGroups = UserGroup::all();

        // Create admin user
        $adminUser = User::factory()->create([
            'name' => 'Site Administrator',
            'email' => 'admin@tasksportal.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'work_position' => 'IT Director',
        ]);
        $adminUser->userGroups()->attach($userGroups->firstWhere('id', 1)->id);

        // Create demo developer user
        $developerUser = User::factory()->create([
            'name' => 'Demo Developer',
            'email' => 'developer@tasksportal.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'work_position' => 'Senior Software Developer',
        ]);
        $developerUser->userGroups()->attach($userGroups->firstWhere('id', 2)->id);

        // Create 18 more users
        User::factory()->count(18)->create()->each(function ($user) use ($userGroups, $faker) {
            $user->userGroups()->attach($userGroups->random()->id);
            $user->work_position = $faker->jobTitle;
            $user->email = $faker->unique()->userName . '@tasksportal.com';
            $user->save();
        });
    }
}
