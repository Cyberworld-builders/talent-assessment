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
     * Get existing assessments (created by other seeders)
     */
    private function createAssessments()
    {
        $assessments = [];
        
        // Get the existing assessments created by other seeders
        $involved360 = Assessment::where('name', 'Involved-360')->first();
        $involvedLeader = Assessment::where('name', 'Involved-Leader')->first();
        $involvedBlockers = Assessment::where('name', 'Involved-Blockers')->first();
        
        if ($involved360) {
            $assessments['involved360'] = $involved360;
        }
        if ($involvedLeader) {
            $assessments['involvedLeader'] = $involvedLeader;
        }
        if ($involvedBlockers) {
            $assessments['involvedBlockers'] = $involvedBlockers;
        }
        
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
            $assessments['involved360']->id,
            $assessments['involvedBlockers']->id
        ], true);
        
        $this->createJob($techClient, 'Product Manager', 'product-manager', [
            $assessments['involvedLeader']->id,
            $assessments['involved360']->id
        ], true);
        
        $this->createJob($techClient, 'Data Scientist', 'data-scientist', [
            $assessments['involved360']->id,
            $assessments['involvedBlockers']->id
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
            'assessments' => [$assessments['involved360']->id, $assessments['involvedLeader']->id],
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
            $assessments['involvedLeader']->id,
            $assessments['involved360']->id
        ], true);
        
        $this->createJob($mfgClient, 'Quality Control Specialist', 'quality-control', [
            $assessments['involved360']->id,
            $assessments['involvedBlockers']->id
        ], true);
        
        $this->createJob($mfgClient, 'Maintenance Technician', 'maintenance-tech', [
            $assessments['involved360']->id
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
            'assessments' => [$assessments['involvedLeader']->id, $assessments['involved360']->id, $assessments['involvedBlockers']->id],
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
            $assessments['involvedLeader']->id,
            $assessments['involved360']->id,
            $assessments['involvedBlockers']->id
        ], true);
        
        $this->createJob($consultClient, 'Business Analyst', 'business-analyst', [
            $assessments['involved360']->id,
            $assessments['involvedBlockers']->id
        ], true);
        
        $this->createJob($consultClient, 'Project Manager', 'project-manager', [
            $assessments['involvedLeader']->id,
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
