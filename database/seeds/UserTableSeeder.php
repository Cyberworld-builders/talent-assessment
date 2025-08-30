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
            'address' => '123 Test Street',
            'primary_color' => '#007bff',
            'accent_color' => '#28a745',
            'whitelabel' => false
        ]);

        // Create admin user with verified email address
        $adminUser = User::create([
            'username' => 'admin-goreman',
            'name' => 'Admin Goreman',
            'email' => 'admin-goreman@cyberworldbuilders.com',
            'password' => bcrypt('password'),
            'client_id' => $client->id,
            'job_title' => 'System Administrator',
            'job_family' => 'Management'
        ]);

        // Assign admin role to the user
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $adminUser->attachRole($adminRole);
        }

        // Create regular user with verified email address
        $regularUser = User::create([
            'username' => 'user-apone',
            'name' => 'User Apone',
            'email' => 'user-apone@cyberworldbuilders.com',
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

        // Create additional test users for development
        $testAdmin = User::create([
            'username' => 'admin',
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'client_id' => $client->id,
            'job_title' => 'Test Administrator',
            'job_family' => 'Management'
        ]);

        if ($adminRole) {
            $testAdmin->attachRole($adminRole);
        }

        $testUser = User::create([
            'username' => 'user',
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'client_id' => $client->id,
            'job_title' => 'Test Employee',
            'job_family' => 'General'
        ]);

        if ($userRole) {
            $testUser->attachRole($userRole);
        }

        echo "Created users with verified email addresses:\n";
        echo "- Admin Goreman: admin-goreman@cyberworldbuilders.com / password (Admin Role)\n";
        echo "- User Apone: user-apone@cyberworldbuilders.com / password (User Role)\n";
        echo "\nAdditional test users:\n";
        echo "- Test Admin: admin@example.com / password (Admin Role)\n";
        echo "- Test User: user@example.com / password (User Role)\n";
    }
} 