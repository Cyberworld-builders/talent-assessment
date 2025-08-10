<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Client;
use Bican\Roles\Models\Role;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create a test client first
        $client = Client::create([
            'name' => 'Test Client',
            'address' => '123 Test Street'
        ]);

        // Create a test user
        $user = User::create([
            'username' => 'admin',
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'client_id' => $client->id,
            'job_title' => 'Administrator',
            'job_family' => 'Management'
        ]);

        // Assign admin role to the user
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $user->attachRole($adminRole);
        }

        // Create a regular user for testing
        $regularUser = User::create([
            'username' => 'user',
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'client_id' => $client->id,
            'job_title' => 'Employee',
            'job_family' => 'General'
        ]);

        // Assign user role to the regular user
        $userRole = Role::where('slug', 'user')->first();
        if ($userRole) {
            $regularUser->attachRole($userRole);
        }

        echo "Created test users:\n";
        echo "- Admin: admin@example.com / password\n";
        echo "- User: user@example.com / password\n";
    }
} 