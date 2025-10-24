<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Client;
use App\User;
use App\Group;
use App\Assessment;
use App\Assignment;
use App\Answer;
use App\Question;
use Bican\Roles\Models\Role;

class StarkIndustries360CampaignSeeder extends Seeder
{
    private $client;
    private $assessment;
    private $users = [];
    private $groups = [];
    private $surveyDate;
    private $questions = [];
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Starting Stark Industries 360 Campaign Seeder...');
        
        // Set a consistent survey date for all assignments
        $this->surveyDate = Carbon::now()->subDays(7);
        
        // Step 1: Create or get the Involved-360 assessment
        $this->getAssessment();
        
        // Step 2: Create Stark Industries client
        $this->createClient();
        
        // Step 3: Create 30 users (Marvel and Disney characters)
        $this->createUsers();
        
        // Step 4: Create 3 groups with targets
        $this->createGroups();
        
        // Step 5: Assign 360 assessments to all users
        $this->createAssignments();
        
        // Step 6: Complete assessments with realistic responses
        $this->completeAssessments();
        
        $this->command->info('✓ Stark Industries 360 Campaign seeded successfully!');
        $this->command->info('');
        $this->command->info('Client: Stark Industries');
        $this->command->info('Total Users: 30');
        $this->command->info('Groups: 3 (Avengers, X-Men, Radiator Springs Racers)');
        $this->command->info('');
        $this->command->info('Sample Login:');
        $this->command->info('Username: tony.stark');
        $this->command->info('Password: password');
    }
    
    /**
     * Get or create the Involved-360 assessment
     */
    private function getAssessment()
    {
        $this->command->info('Getting Involved-360 assessment...');
        
        $this->assessment = Assessment::where('name', 'Involved-360 - No BARS, testing')->first();
        
        if (!$this->assessment) {
            $this->command->error('Involved-360 assessment not found!');
            $this->command->error('Please run: php artisan db:seed --class=Involved360AssessmentSeeder');
            throw new Exception('Involved-360 assessment not found');
        }
        
        // Get all questions for this assessment
        $this->questions = Question::where('assessment_id', $this->assessment->id)
            ->orderBy('number')
            ->get();
        
        $this->command->info('✓ Found assessment with ' . $this->questions->count() . ' questions');
    }
    
    /**
     * Create Stark Industries client
     */
    private function createClient()
    {
        $this->command->info('Creating Stark Industries client...');
        
        // Check if client already exists
        $existingClient = Client::where('name', 'Stark Industries')->first();
        if ($existingClient) {
            $this->command->warn('Stark Industries already exists. Deleting and recreating...');
            
            // Delete related data
            User::where('client_id', $existingClient->id)->delete();
            Group::where('client_id', $existingClient->id)->delete();
            $existingClient->delete();
        }
        
        $this->client = Client::create([
            'name' => 'Stark Industries',
            'address' => '200 Park Avenue, New York, NY 10166',
            'logo' => null,
            'background' => null,
            'assessments' => [$this->assessment->id],
            'require_profile' => true,
            'require_research' => false,
            'whitelabel' => false,
            'primary_color' => '#C8102E',
            'accent_color' => '#FFD700'
        ]);
        
        $this->command->info('✓ Created Stark Industries (ID: ' . $this->client->id . ')');
    }
    
    /**
     * Create 30 users from Marvel and Disney characters
     */
    private function createUsers()
    {
        $this->command->info('Creating 30 users...');
        
        $userRole = Role::where('slug', 'user')->first();
        
        // Group 1: Avengers
        $avengersUsers = [
            ['name' => 'Tony Stark', 'email' => 'tony.stark@starkindustries.com', 'title' => 'CEO'],
            ['name' => 'Steve Rogers', 'email' => 'steve.rogers@starkindustries.com', 'title' => 'Director of Operations'],
            ['name' => 'Bruce Banner', 'email' => 'bruce.banner@starkindustries.com', 'title' => 'Chief Scientist'],
            ['name' => 'Natasha Romanoff', 'email' => 'natasha.romanoff@starkindustries.com', 'title' => 'Director of Security'],
            ['name' => 'Thor', 'email' => 'thor@starkindustries.com', 'title' => 'VP of Energy'],
            ['name' => 'Clint Barton', 'email' => 'clint.barton@starkindustries.com', 'title' => 'VP of Precision Engineering'],
            ['name' => 'Nick Fury', 'email' => 'nick.fury@starkindustries.com', 'title' => 'Chief Strategy Officer'],
            ['name' => 'Agent Coulson', 'email' => 'agent.coulson@starkindustries.com', 'title' => 'Senior Manager'],
            ['name' => 'Colby Wilson', 'email' => 'colby.wilson@starkindustries.com', 'title' => 'Project Manager'],
            ['name' => 'Jarvis', 'email' => 'jarvis@starkindustries.com', 'title' => 'AI Systems Administrator'],
        ];
        
        // Group 2: X-Men
        $xmenUsers = [
            ['name' => 'Wolverine', 'email' => 'wolverine@starkindustries.com', 'title' => 'VP of Manufacturing'],
            ['name' => 'Cyclops', 'email' => 'cyclops@starkindustries.com', 'title' => 'Director of Vision Tech'],
            ['name' => 'Storm', 'email' => 'storm@starkindustries.com', 'title' => 'VP of Environmental Systems'],
            ['name' => 'Jean Grey', 'email' => 'jean.grey@starkindustries.com', 'title' => 'Chief Innovation Officer'],
            ['name' => 'Iceman', 'email' => 'iceman@starkindustries.com', 'title' => 'VP of Cryogenics'],
            ['name' => 'Beast', 'email' => 'beast@starkindustries.com', 'title' => 'Chief Technology Officer'],
            ['name' => 'Angel', 'email' => 'angel@starkindustries.com', 'title' => 'VP of Aviation'],
            ['name' => 'Shadowcat', 'email' => 'shadowcat@starkindustries.com', 'title' => 'Senior Developer'],
            ['name' => 'Jubilee', 'email' => 'jubilee@starkindustries.com', 'title' => 'Marketing Manager'],
            ['name' => 'Gambit', 'email' => 'gambit@starkindustries.com', 'title' => 'Director of Risk Management'],
        ];
        
        // Group 3: Radiator Springs Racers
        $racersUsers = [
            ['name' => 'Lightning McQueen', 'email' => 'lightning.mcqueen@starkindustries.com', 'title' => 'VP of Automotive Division'],
            ['name' => 'Mater', 'email' => 'mater@starkindustries.com', 'title' => 'Director of Maintenance'],
            ['name' => 'Doc Hudson', 'email' => 'doc.hudson@starkindustries.com', 'title' => 'Senior Advisor'],
            ['name' => 'Sally Carrera', 'email' => 'sally.carrera@starkindustries.com', 'title' => 'VP of Community Relations'],
            ['name' => 'Ramone', 'email' => 'ramone@starkindustries.com', 'title' => 'Director of Design'],
            ['name' => 'Strip Weathers', 'email' => 'strip.weathers@starkindustries.com', 'title' => 'Executive Consultant'],
            ['name' => 'Fillmore', 'email' => 'fillmore@starkindustries.com', 'title' => 'VP of Alternative Energy'],
            ['name' => 'Sheriff', 'email' => 'sheriff@starkindustries.com', 'title' => 'Director of Compliance'],
            ['name' => 'Flo', 'email' => 'flo@starkindustries.com', 'title' => 'VP of Hospitality'],
            ['name' => 'Luigi', 'email' => 'luigi@starkindustries.com', 'title' => 'Director of Supply Chain'],
        ];
        
        $allUsers = array_merge($avengersUsers, $xmenUsers, $racersUsers);
        
        foreach ($allUsers as $userData) {
            $username = strtolower(str_replace(' ', '.', $userData['name']));
            
            $user = User::create([
                'username' => $username,
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => bcrypt('password'),
                'client_id' => $this->client->id,
                'job_title' => $userData['title'],
                'job_family' => 'Management'
            ]);
            
            if ($userRole) {
                $user->attachRole($userRole);
            }
            
            $this->users[] = $user;
        }
        
        $this->command->info('✓ Created ' . count($this->users) . ' users');
    }
    
    /**
     * Create 3 groups with targets
     */
    private function createGroups()
    {
        $this->command->info('Creating groups...');
        
        // Group 1: Avengers (Tony Stark is the target)
        $avengersTarget = $this->users[0]; // Tony Stark
        $avengersMembers = array_slice($this->users, 0, 10);
        $this->createGroup('Avengers', 'Avengers Leadership Team', $avengersTarget, $avengersMembers);
        
        // Group 2: X-Men (Cyclops is the target)
        $xmenTarget = $this->users[11]; // Cyclops
        $xmenMembers = array_slice($this->users, 10, 10);
        $this->createGroup('X-Men', 'X-Men Division', $xmenTarget, $xmenMembers);
        
        // Group 3: Radiator Springs Racers (Lightning McQueen is the target)
        $racersTarget = $this->users[20]; // Lightning McQueen
        $racersMembers = array_slice($this->users, 20, 10);
        $this->createGroup('Radiator Springs Racers', 'Automotive Division Team', $racersTarget, $racersMembers);
        
        $this->command->info('✓ Created 3 groups with targets');
    }
    
    /**
     * Create a single group
     */
    private function createGroup($name, $description, $target, $members)
    {
        $groupUsers = [];
        
        foreach ($members as $i => $member) {
            // Determine relationship to target
            if ($member->id == $target->id) {
                $position = 'Self';
            } elseif ($i < 3) {
                $position = 'Direct Report';
            } elseif ($i < 5) {
                $position = 'Supervisor';
            } elseif ($i < 8) {
                $position = 'Peer';
            } else {
                $position = 'Other';
            }
            
            $groupUsers[] = [
                'id' => $member->id,
                'position' => $position,
                'leader' => 0
            ];
        }
        
        $group = new Group([
            'name' => $name,
            'description' => $description,
            'users' => $groupUsers,
            'target_id' => $target->id
        ]);
        
        $this->client->groups()->save($group);
        $this->groups[] = [
            'group' => $group,
            'target' => $target,
            'members' => $members
        ];
    }
    
    /**
     * Create assignments for all users in groups
     */
    private function createAssignments()
    {
        $this->command->info('Creating assignments...');
        
        $assignmentCount = 0;
        
        foreach ($this->groups as $groupData) {
            $target = $groupData['target'];
            $members = $groupData['members'];
            
            foreach ($members as $member) {
                // Determine relationship to target
                $relationship = $this->getUserRelationship($member, $target, $members);
                
                // Create custom fields
                $customFields = [
                    'type' => ['name', 'email', 'role'],
                    'value' => [$target->name, $target->email, $relationship]
                ];
                
                // Create assignment
                $assignment = new Assignment([
                    'assessment_id' => $this->assessment->id,
                    'expires' => Carbon::now()->addDays(30),
                    'whitelabel' => 0,
                    'target_id' => $target->id,
                    'custom_fields' => $customFields,
                    'completed' => 1,
                    'started_at' => $this->surveyDate,
                    'completed_at' => $this->surveyDate->copy()->addHours(rand(1, 48))
                ]);
                
                $member->assignments()->save($assignment);
                
                // Force the created_at to be the survey date
                $assignment->created_at = $this->surveyDate;
                $assignment->save();
                
                $assignmentCount++;
            }
        }
        
        $this->command->info('✓ Created ' . $assignmentCount . ' assignments');
    }
    
    /**
     * Determine user's relationship to target
     */
    private function getUserRelationship($user, $target, $members)
    {
        if ($user->id == $target->id) {
            return 'Self';
        }
        
        $userIndex = array_search($user, $members);
        
        if ($userIndex < 3) {
            return 'Direct Report';
        } elseif ($userIndex < 5) {
            return 'Supervisor';
        } elseif ($userIndex < 8) {
            return 'Peer';
        } else {
            return 'Other';
        }
    }
    
    /**
     * Complete all assignments with realistic responses
     */
    private function completeAssessments()
    {
        $this->command->info('Completing assessments with responses...');
        
        $answerCount = 0;
        
        foreach ($this->groups as $groupData) {
            $target = $groupData['target'];
            $members = $groupData['members'];
            
            foreach ($members as $member) {
                $assignment = Assignment::where([
                    'user_id' => $member->id,
                    'target_id' => $target->id,
                    'assessment_id' => $this->assessment->id
                ])->where('created_at', $this->surveyDate)->first();
                
                if ($assignment) {
                    $relationship = $this->getUserRelationship($member, $target, $members);
                    $answers = $this->generateAnswersForAssignment($assignment, $relationship, $target);
                    $answerCount += count($answers);
                }
            }
        }
        
        $this->command->info('✓ Created ' . $answerCount . ' answers');
    }
    
    /**
     * Generate realistic answers for an assignment
     */
    private function generateAnswersForAssignment($assignment, $relationship, $target)
    {
        $answers = [];
        
        // Define performance profiles for each target to create trends
        $performanceProfiles = [
            'Tony Stark' => [
                'Creative Problem Solving' => 4.5,
                'Leadership Adaptability' => 4.0,
                'Collaboration' => 3.5,
                'Self-Development' => 4.8,
                'Performance Management' => 3.8,
                'Business Mindset' => 4.9
            ],
            'Cyclops' => [
                'Creative Problem Solving' => 3.8,
                'Leadership Adaptability' => 4.2,
                'Collaboration' => 4.5,
                'Self-Development' => 3.9,
                'Performance Management' => 4.3,
                'Business Mindset' => 3.7
            ],
            'Lightning McQueen' => [
                'Creative Problem Solving' => 3.5,
                'Leadership Adaptability' => 3.2,
                'Collaboration' => 3.0,
                'Self-Development' => 4.5,
                'Performance Management' => 3.5,
                'Business Mindset' => 3.8
            ]
        ];
        
        $profile = $performanceProfiles[$target->name] ?? [
            'Creative Problem Solving' => 3.5,
            'Leadership Adaptability' => 3.5,
            'Collaboration' => 3.5,
            'Self-Development' => 3.5,
            'Performance Management' => 3.5,
            'Business Mindset' => 3.5
        ];
        
        // Adjust scores based on relationship (bias simulation)
        $relationshipModifiers = [
            'Self' => 0.3,  // People rate themselves higher
            'Direct Report' => 0.2,  // Direct reports rate higher
            'Supervisor' => -0.1,  // Supervisors are more critical
            'Peer' => 0.0,  // Peers are most accurate
            'Other' => 0.1
        ];
        
        $modifier = $relationshipModifiers[$relationship] ?? 0;
        
        foreach ($this->questions as $question) {
            $answer = null;
            
            // Type 1: Rating question
            if ($question->type == 1) {
                $dimensionName = $question->dimension() ? $question->dimension()->name : null;
                $baseScore = $profile[$dimensionName] ?? 3.5;
                
                // Apply relationship modifier and add some randomness
                $score = $baseScore + $modifier + (rand(-5, 5) / 10);
                $score = max(1, min(5, $score)); // Clamp between 1 and 5
                
                // Convert to 0-4 scale (internal storage)
                $value = round($score) - 1;
                
                $answer = new Answer([
                    'assignment_id' => $assignment->id,
                    'question_id' => $question->id,
                    'value' => $value
                ]);
            }
            
            // Type 3: Text feedback question
            elseif ($question->type == 3) {
                // Generate feedback 70% of the time
                if (rand(1, 10) > 3) {
                    $dimensionName = $question->dimension() ? $question->dimension()->name : 'General';
                    $feedback = $this->generateFeedback($target->name, $dimensionName, $relationship, $profile[$dimensionName] ?? 3.5);
                    
                    $answer = new Answer([
                        'assignment_id' => $assignment->id,
                        'question_id' => $question->id,
                        'value' => $feedback
                    ]);
                }
            }
            
            if ($answer) {
                $answer->save();
                $answers[] = $answer;
            }
        }
        
        return $answers;
    }
    
    /**
     * Generate realistic feedback text
     */
    private function generateFeedback($targetName, $dimension, $relationship, $score)
    {
        $firstName = explode(' ', $targetName)[0];
        
        // Positive feedback (score >= 4)
        $positiveFeedback = [
            'Creative Problem Solving' => [
                "{$firstName} consistently develops innovative solutions to complex challenges.",
                "{$firstName} demonstrates exceptional creativity in problem-solving approaches.",
                "I've seen {$firstName} tackle difficult problems with fresh, innovative thinking.",
                "{$firstName} has a unique ability to see solutions that others miss.",
            ],
            'Leadership Adaptability' => [
                "{$firstName} adapts quickly to changing business conditions.",
                "{$firstName} leads change initiatives with confidence and positivity.",
                "I appreciate how {$firstName} helps the team navigate through organizational changes.",
                "{$firstName} is always ready to adjust strategies when needed.",
            ],
            'Collaboration' => [
                "{$firstName} works exceptionally well with team members across departments.",
                "{$firstName} builds strong collaborative relationships.",
                "I enjoy working with {$firstName} - they make collaboration easy and productive.",
                "{$firstName} is a true team player who brings people together.",
            ],
            'Self-Development' => [
                "{$firstName} is constantly seeking opportunities for growth and development.",
                "{$firstName} demonstrates strong commitment to continuous learning.",
                "I've noticed {$firstName} actively working on improving their skills.",
                "{$firstName} embraces feedback and uses it to improve.",
            ],
            'Performance Management' => [
                "{$firstName} excels at setting clear expectations and providing feedback.",
                "{$firstName} holds team members accountable while remaining supportive.",
                "I appreciate {$firstName}'s approach to performance management.",
                "{$firstName} provides timely and constructive feedback.",
            ],
            'Business Mindset' => [
                "{$firstName} demonstrates strong business acumen.",
                "{$firstName} consistently makes decisions with the bigger picture in mind.",
                "I value {$firstName}'s strategic thinking and business perspective.",
                "{$firstName} understands how our work impacts the bottom line.",
            ]
        ];
        
        // Constructive feedback (score < 4)
        $constructiveFeedback = [
            'Creative Problem Solving' => [
                "{$firstName} could benefit from exploring more innovative approaches to challenges.",
                "I'd like to see {$firstName} think more outside the box when solving problems.",
                "{$firstName} sometimes sticks to traditional solutions when creativity could add value.",
            ],
            'Leadership Adaptability' => [
                "{$firstName} could improve in adapting to rapid changes.",
                "I'd suggest {$firstName} work on being more flexible with changing priorities.",
                "{$firstName} sometimes struggles when plans need to shift quickly.",
            ],
            'Collaboration' => [
                "{$firstName} could strengthen relationships with peers in other departments.",
                "I'd like to see {$firstName} seek input from more team members.",
                "{$firstName} tends to work independently when collaboration might be beneficial.",
            ],
            'Self-Development' => [
                "{$firstName} could be more proactive in seeking development opportunities.",
                "I'd encourage {$firstName} to pursue more training and skill development.",
                "{$firstName} might benefit from being more open to feedback.",
            ],
            'Performance Management' => [
                "{$firstName} could provide more regular performance feedback.",
                "I'd like to see {$firstName} hold team members more accountable.",
                "{$firstName} sometimes avoids difficult performance conversations.",
            ],
            'Business Mindset' => [
                "{$firstName} could develop stronger business acumen.",
                "I'd suggest {$firstName} focus more on strategic business outcomes.",
                "{$firstName} might benefit from understanding the broader business context.",
            ]
        ];
        
        $feedbackPool = $score >= 4 ? $positiveFeedback : $constructiveFeedback;
        $dimensionFeedback = $feedbackPool[$dimension] ?? $feedbackPool['Creative Problem Solving'];
        
        return $dimensionFeedback[array_rand($dimensionFeedback)];
    }
}

