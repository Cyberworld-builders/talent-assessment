<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create roles with spatie/laravel-permission
        $roles = [
            [
                'name' => 'AOE Admin',
                'guard_name' => 'web',
                'level' => 4,
            ],
            [
                'name' => 'Reseller',
                'guard_name' => 'web',
                'level' => 3,
            ],
            [
                'name' => 'Client Admin',
                'guard_name' => 'web',
                'level' => 2,
            ],
            [
                'name' => 'User',
                'guard_name' => 'web',
                'level' => 1,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name'], 'guard_name' => $roleData['guard_name']],
                [
                    'slug' => strtolower(str_replace(' ', '-', $roleData['name'])),
                    'description' => $roleData['name'] . ' role',
                    'level' => $roleData['level'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]
            );
        }

        $this->command->info('Roles seeded successfully!');
    }
}
