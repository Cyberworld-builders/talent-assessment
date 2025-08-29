<?php

use Illuminate\Database\Seeder;
use App\Client;
use App\Job;
use App\Assessment;
use App\User;
use Bican\Roles\Models\Role;

class ClientTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create sample assessments first
        $assessments = $this->createAssessments();
        
        // Create multiple clients with different scenarios
        $this->createTechCompany($assessments);
        $this->createManufacturingCompany($assessments);
        $this->createConsultingFirm($assessments);
        
        echo "Created test clients with jobs and users for Selection tab testing:\n";
        echo "- TechCorp: techadmin@techcorp.com / password\n";
        echo "- Manufacturing Inc: mfgadmin@manufacturing.com / password\n";
        echo "- Consulting Partners: consultadmin@consulting.com / password\n";
    }
    
    /**
     * Create assessments from dump data
     */
    private function createAssessments()
    {
        $assessments = [];
        
        // Get the admin user for assessments
        $adminUser = User::where('email', 'admin@example.com')->first();
        
        // Create Involved-360 assessment
        $involved360 = $adminUser->assessments()->create([
            'id' => 1,
            'name' => 'Involved-360',
            'description' => 'A competency based 360-evaluation that provides an analytically robust picture of strengths and improvement opportunities.',
            'logo' => 'https://involved.sfo2.digitaloceanspaces.com/uploads/Involved-360.png',
            'background' => '',
            'paginate' => 1,
            'items_per_page' => 4,
            'translation' => null,
            'language' => null,
            'whitelabel' => null,
            'company_labeled_for' => null,
            'timed' => '0',
            'time_limit' => 10,
            'use_custom_fields' => 1,
            'custom_fields' => 'a:2:{s:3:"tag";a:2:{i:0;s:4:"name";i:1;s:5:"email";}s:7:"default";a:2:{i:0;s:0:"";i:1;s:0:"";}}',
            'target' => 1,
            'created_at' => '2020-01-27 02:23:14',
            'updated_at' => '2021-02-12 19:34:53',
            'last_modified' => '0000-00-00 00:00:00'
        ]);
        $assessments['involved360'] = $involved360;
        
        // Create Involved-Leader assessment
        $involvedLeader = $adminUser->assessments()->create([
            'id' => 3,
            'name' => 'Involved-Leader',
            'description' => 'A multi-rater diagnostic inventory that dives deep into scientifically grounded and analytically proven drivers of leadership effectiveness.',
            'logo' => 'https://involved.sfo2.digitaloceanspaces.com/uploads/Involved-Leader.png',
            'background' => '',
            'paginate' => 1,
            'items_per_page' => 12,
            'translation' => null,
            'language' => null,
            'whitelabel' => null,
            'company_labeled_for' => null,
            'timed' => '0',
            'time_limit' => 10,
            'use_custom_fields' => 1,
            'custom_fields' => 'a:2:{s:3:"tag";a:2:{i:0;s:4:"name";i:1;s:5:"email";}s:7:"default";a:2:{i:0;s:0:"";i:1;s:0:"";}}',
            'target' => 1,
            'created_at' => '2020-04-02 07:48:24',
            'updated_at' => '2021-05-06 15:08:29',
            'last_modified' => '0000-00-00 00:00:00'
        ]);
        $assessments['involvedLeader'] = $involvedLeader;
        
        // Create Involved-Blockers assessment
        $involvedBlockers = $adminUser->assessments()->create([
            'id' => 4,
            'name' => 'Involved-Blockers',
            'description' => 'Identifies personality attributes that are preventing you from realizing your full involvement and leadership potential.',
            'logo' => 'https://involved.sfo2.digitaloceanspaces.com/uploads/Involved-Blockers.png',
            'background' => '',
            'paginate' => 1,
            'items_per_page' => 10,
            'translation' => null,
            'language' => null,
            'whitelabel' => null,
            'company_labeled_for' => null,
            'timed' => '0',
            'time_limit' => 10,
            'use_custom_fields' => 0,
            'custom_fields' => 'N;',
            'target' => 0,
            'created_at' => '2020-04-11 10:34:35',
            'updated_at' => '2020-12-11 20:17:18',
            'last_modified' => '0000-00-00 00:00:00'
        ]);
        $assessments['involvedBlockers'] = $involvedBlockers;
        
        // Create Involved-Me assessment
        $involvedMe = $adminUser->assessments()->create([
            'id' => 5,
            'name' => 'Involved-Me',
            'description' => 'A self-report version of the Involved-Leader, providing great insight into scientifically grounded, yet analytically proven drivers of leadership effectiveness.',
            'logo' => 'https://involved.sfo2.digitaloceanspaces.com/uploads/Involved-Me.png',
            'background' => '',
            'paginate' => 1,
            'items_per_page' => 12,
            'translation' => null,
            'language' => null,
            'whitelabel' => null,
            'company_labeled_for' => null,
            'timed' => '0',
            'time_limit' => 10,
            'use_custom_fields' => 0,
            'custom_fields' => 'N;',
            'target' => 0,
            'created_at' => '2020-06-13 16:14:27',
            'updated_at' => '2020-06-26 20:59:36',
            'last_modified' => '0000-00-00 00:00:00'
        ]);
        $assessments['involvedMe'] = $involvedMe;
        
        // Create Involved-Me Peak Week assessment
        $involvedMePeak = $adminUser->assessments()->create([
            'id' => 6,
            'name' => 'Involved-Me Peak Week',
            'description' => 'A self-report version of the Involved-Leader, providing great insight into scientifically grounded, yet analytically proven drivers of leadership effectiveness.',
            'logo' => 'https://involved.sfo2.digitaloceanspaces.com/uploads/Involved-Me.png',
            'background' => '',
            'paginate' => 1,
            'items_per_page' => 12,
            'translation' => null,
            'language' => null,
            'whitelabel' => null,
            'company_labeled_for' => null,
            'timed' => '0',
            'time_limit' => 10,
            'use_custom_fields' => 0,
            'custom_fields' => 'N;',
            'target' => 0,
            'created_at' => '2020-06-13 16:14:27',
            'updated_at' => '2020-08-27 13:12:28',
            'last_modified' => '2020-06-26 20:59:36'
        ]);
        $assessments['involvedMePeak'] = $involvedMePeak;
        
        // Create David Codes assessment
        $davidCodes = $adminUser->assessments()->create([
            'id' => 7,
            'name' => 'David Codes',
            'description' => 'noob',
            'logo' => '',
            'background' => '',
            'paginate' => 0,
            'items_per_page' => 0,
            'translation' => null,
            'language' => null,
            'whitelabel' => null,
            'company_labeled_for' => null,
            'timed' => '0',
            'time_limit' => 10,
            'use_custom_fields' => 0,
            'custom_fields' => 'N;',
            'target' => 0,
            'created_at' => '2025-07-31 19:19:23',
            'updated_at' => '2025-07-31 19:19:23',
            'last_modified' => '2025-07-31 19:19:23'
        ]);
        $assessments['davidCodes'] = $davidCodes;
        
        return $assessments;
    }
    
    /**
     * Create a technology company with multiple jobs
     */
    private function createTechCompany($assessments)
    {
        // Create the client
        $techClient = Client::create([
            'name' => 'TechCorp Solutions',
            'address' => '123 Innovation Drive, Silicon Valley, CA 94025',
            'logo' => null,
            'background' => null,
            'assessments' => [$assessments['involved360']->id, $assessments['involvedLeader']->id],
            'require_profile' => true,
            'require_research' => false,
            'whitelabel' => false,
            'primary_color' => '#007bff',
            'accent_color' => '#28a745'
        ]);
        
        // Create admin user for TechCorp
        $techAdmin = User::create([
            'username' => 'techadmin',
            'name' => 'Sarah Johnson',
            'email' => 'techadmin@techcorp.com',
            'password' => bcrypt('password'),
            'client_id' => $techClient->id,
            'job_title' => 'HR Director',
            'job_family' => 'Human Resources'
        ]);
        
        // Assign admin role
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $techAdmin->attachRole($adminRole);
        }
        
        // Create jobs for TechCorp
        $this->createJob($techClient, 'Software Engineer', 'software-engineer', [
            $assessments['involvedLeader']->id,
            $assessments['involved360']->id
        ], true);
        
        $this->createJob($techClient, 'Product Manager', 'product-manager', [
            $assessments['involvedBlockers']->id,
            $assessments['involved360']->id
        ], true);
        
        $this->createJob($techClient, 'Data Scientist', 'data-scientist', [
            $assessments['involvedLeader']->id,
            $assessments['involved360']->id
        ], false); // Closed job
        
        // Create some applicant users
        $this->createApplicants($techClient, 8);
    }
    
    /**
     * Create a manufacturing company
     */
    private function createManufacturingCompany($assessments)
    {
        // Create the client
        $mfgClient = Client::create([
            'name' => 'Manufacturing Inc',
            'address' => '456 Industrial Blvd, Detroit, MI 48201',
            'logo' => null,
            'background' => null,
            'assessments' => [$assessments['involved360']->id, $assessments['involvedBlockers']->id],
            'require_profile' => true,
            'require_research' => true,
            'whitelabel' => false,
            'primary_color' => '#dc3545',
            'accent_color' => '#ffc107'
        ]);
        
        // Create admin user for Manufacturing Inc
        $mfgAdmin = User::create([
            'username' => 'mfgadmin',
            'name' => 'Michael Chen',
            'email' => 'mfgadmin@manufacturing.com',
            'password' => bcrypt('password'),
            'client_id' => $mfgClient->id,
            'job_title' => 'Operations Manager',
            'job_family' => 'Operations'
        ]);
        
        // Assign admin role
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $mfgAdmin->attachRole($adminRole);
        }
        
        // Create jobs for Manufacturing Inc
        $this->createJob($mfgClient, 'Production Supervisor', 'production-supervisor', [
            $assessments['involvedBlockers']->id,
            $assessments['involved360']->id
        ], true);
        
        $this->createJob($mfgClient, 'Quality Control Specialist', 'quality-control', [
            $assessments['involvedLeader']->id,
            $assessments['involved360']->id
        ], true);
        
        $this->createJob($mfgClient, 'Maintenance Technician', 'maintenance-tech', [
            $assessments['involvedLeader']->id
        ], true);
        
        // Create some applicant users
        $this->createApplicants($mfgClient, 12);
    }
    
    /**
     * Create a consulting firm
     */
    private function createConsultingFirm($assessments)
    {
        // Create the client
        $consultClient = Client::create([
            'name' => 'Consulting Partners',
            'address' => '789 Business Center, New York, NY 10001',
            'logo' => null,
            'background' => null,
            'assessments' => [$assessments['involvedBlockers']->id, $assessments['involved360']->id, $assessments['involvedLeader']->id],
            'require_profile' => true,
            'require_research' => true,
            'whitelabel' => false,
            'primary_color' => '#6f42c1',
            'accent_color' => '#fd7e14'
        ]);
        
        // Create admin user for Consulting Partners
        $consultAdmin = User::create([
            'username' => 'consultadmin',
            'name' => 'Jennifer Rodriguez',
            'email' => 'consultadmin@consulting.com',
            'password' => bcrypt('password'),
            'client_id' => $consultClient->id,
            'job_title' => 'Talent Acquisition Manager',
            'job_family' => 'Human Resources'
        ]);
        
        // Assign admin role
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $consultAdmin->attachRole($adminRole);
        }
        
        // Create jobs for Consulting Partners
        $this->createJob($consultClient, 'Senior Consultant', 'senior-consultant', [
            $assessments['involvedBlockers']->id,
            $assessments['involvedLeader']->id,
            $assessments['involved360']->id
        ], true);
        
        $this->createJob($consultClient, 'Business Analyst', 'business-analyst', [
            $assessments['involvedLeader']->id,
            $assessments['involved360']->id
        ], true);
        
        $this->createJob($consultClient, 'Project Manager', 'project-manager', [
            $assessments['involvedBlockers']->id,
            $assessments['involved360']->id
        ], false); // Closed job
        
        // Create some applicant users
        $this->createApplicants($consultClient, 15);
    }
    
    /**
     * Create a job for a client
     */
    private function createJob($client, $name, $slug, $assessmentIds, $active = true)
    {
        return Job::create([
            'name' => $name,
            'slug' => $slug,
            'description' => "Job description for {$name} position at {$client->name}",
            'client_id' => $client->id,
            'active' => $active,
            'assessments' => $assessmentIds,
            'reseller_id' => null,
            'job_template_id' => null
        ]);
    }
    
    /**
     * Create applicant users for a client
     */
    private function createApplicants($client, $count)
    {
        $firstNames = ['Alex', 'Jordan', 'Taylor', 'Casey', 'Morgan', 'Riley', 'Quinn', 'Avery', 'Blake', 'Cameron'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez'];
        $jobTitles = ['Analyst', 'Specialist', 'Coordinator', 'Assistant', 'Representative', 'Technician', 'Consultant'];
        $jobFamilies = ['Technology', 'Operations', 'Sales', 'Marketing', 'Finance', 'Human Resources'];
        
        for ($i = 0; $i < $count; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $fullName = $firstName . ' ' . $lastName;
            $username = strtolower($firstName . $lastName . rand(1, 999));
            $email = strtolower($firstName . '.' . $lastName . '@example.com');
            
            User::create([
                'username' => $username,
                'name' => $fullName,
                'email' => $email,
                'password' => bcrypt('password'),
                'client_id' => $client->id,
                'job_title' => $jobTitles[array_rand($jobTitles)],
                'job_family' => $jobFamilies[array_rand($jobFamilies)]
            ]);
        }
    }
}
