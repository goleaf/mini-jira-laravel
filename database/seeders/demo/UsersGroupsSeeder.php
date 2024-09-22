<?php

namespace Database\Seeders\demo;

use Illuminate\Database\Seeder;
use App\Models\UserGroup;

class UsersGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usersGroups = [
            [
                'name' => 'IT Management',
                'description' => 'Senior IT managers and decision makers',
            ],
            [
                'name' => 'Software Developers',
                'description' => 'Programmers and software engineers',
            ],
            [
                'name' => 'QA Testers',
                'description' => 'Quality assurance and testing team',
            ],
            [
                'name' => 'UI/UX Designers',
                'description' => 'User interface and experience designers',
            ],
            [
                'name' => 'System Administrators',
                'description' => 'IT infrastructure and system maintenance team',
            ],
            [
                'name' => 'Project Managers',
                'description' => 'IT project coordination and management',
            ],
            [
                'name' => 'Data Analysts',
                'description' => 'Data processing and analysis specialists',
            ],
            [
                'name' => 'Customer Support',
                'description' => 'Technical support and customer service team',
            ],
            [
                'name' => 'HR & Admin',
                'description' => 'Human resources and administrative staff',
            ],
            [
                'name' => 'Interns',
                'description' => 'Temporary staff and interns in various departments',
            ],
        ];

        foreach ($usersGroups as $group) {
            UserGroup::create($group);
        }
    }
}
