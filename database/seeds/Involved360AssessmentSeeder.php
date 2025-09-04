<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Involved360AssessmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Seeding Involved-360 Assessment...');

        // Create the assessment
        $assessmentId = $this->createAssessment();
        
        // Create dimensions
        $this->createDimensions($assessmentId);
        
        // Create questions
        $this->createQuestions($assessmentId);
        
        // Create industry benchmarks
        $this->createIndustryBenchmarks();
        
        $this->command->info('Involved-360 Assessment seeded successfully!');
    }

    /**
     * Create the main assessment record
     */
    private function createAssessment()
    {
        $assessmentId = DB::table('assessments')->insertGetId([
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
            'custom_fields' => serialize([
                'tag' => ['name', 'email'],
                'default' => ['', '']
            ]),
            'target' => 1,
            'last_modified' => Carbon::now(),
            'created_at' => Carbon::parse('2020-01-27T02:23:14.000000Z'),
            'updated_at' => Carbon::parse('2021-02-12T19:34:53.000000Z'),
            'user_id' => 1 // Assuming user ID 1 exists
        ]);

        $this->command->info("Created assessment with ID: {$assessmentId}");
        return $assessmentId;
    }

    /**
     * Create the assessment dimensions
     */
    private function createDimensions($assessmentId)
    {
        $dimensions = [
            [
                'name' => 'Creative Problem Solving',
                'parent' => 0,
                'code' => 'CPS',
                'assessment_id' => $assessmentId,
                'created_at' => Carbon::parse('2020-01-27T02:25:50.000000Z'),
                'updated_at' => Carbon::parse('2020-01-27T02:25:50.000000Z')
            ],
            [
                'name' => 'Leadership Adaptability',
                'parent' => 0,
                'code' => 'LA',
                'assessment_id' => $assessmentId,
                'created_at' => Carbon::parse('2020-01-27T23:13:01.000000Z'),
                'updated_at' => Carbon::parse('2020-01-27T23:13:01.000000Z')
            ],
            [
                'name' => 'Collaboration',
                'parent' => 0,
                'code' => 'CO',
                'assessment_id' => $assessmentId,
                'created_at' => Carbon::parse('2020-01-27T23:19:30.000000Z'),
                'updated_at' => Carbon::parse('2020-02-29T14:14:55.000000Z')
            ],
            [
                'name' => 'Self-Development',
                'parent' => 0,
                'code' => 'SD',
                'assessment_id' => $assessmentId,
                'created_at' => Carbon::parse('2020-01-27T23:29:17.000000Z'),
                'updated_at' => Carbon::parse('2020-01-27T23:30:47.000000Z')
            ],
            [
                'name' => 'Performance Management',
                'parent' => 0,
                'code' => 'PM',
                'assessment_id' => $assessmentId,
                'created_at' => Carbon::parse('2020-01-27T23:29:38.000000Z'),
                'updated_at' => Carbon::parse('2020-01-27T23:29:38.000000Z')
            ],
            [
                'name' => 'Business Mindset',
                'parent' => 0,
                'code' => 'BM',
                'assessment_id' => $assessmentId,
                'created_at' => Carbon::parse('2020-01-27T23:29:54.000000Z'),
                'updated_at' => Carbon::parse('2020-01-27T23:29:54.000000Z')
            ],
            [
                'name' => 'Customer Focus',
                'parent' => 0,
                'code' => 'CF',
                'assessment_id' => $assessmentId,
                'created_at' => Carbon::parse('2020-02-29T14:48:25.000000Z'),
                'updated_at' => Carbon::parse('2020-02-29T14:48:25.000000Z')
            ],
            [
                'name' => 'Communication',
                'parent' => 0,
                'code' => 'COM',
                'assessment_id' => $assessmentId,
                'created_at' => Carbon::parse('2020-02-29T14:56:50.000000Z'),
                'updated_at' => Carbon::parse('2020-02-29T14:56:50.000000Z')
            ],
            [
                'name' => 'Ethics & Integrity',
                'parent' => 0,
                'code' => 'E&I',
                'assessment_id' => $assessmentId,
                'created_at' => Carbon::parse('2020-02-29T15:01:11.000000Z'),
                'updated_at' => Carbon::parse('2020-02-29T15:01:11.000000Z')
            ]
        ];

        foreach ($dimensions as $dimension) {
            DB::table('dimensions')->insert($dimension);
        }

        $this->command->info('Created ' . count($dimensions) . ' dimensions');
    }

    /**
     * Create the assessment questions
     */
    private function createQuestions($assessmentId)
    {
        $questions = [
            // Creative Problem Solving - Instructions
            [
                'content' => '<h2>Creative Problem Solving</h2><p><strong>Definition</strong>: Creative Problem Solving is defined as proactive problem-solving focusing on the successful implementation of novel and creative solutions to known or anticipated problems, being mindful of and efficient in using resources (i.e., resourcefulness), and utilizing a visionary mindset.</p><h3 style="color:rgb(170, 170, 170); font-style:italic"><small><big>Please select your rating for&nbsp;[name] for this dimension.</big></small></h3>',
                'assessment_id' => $assessmentId,
                'number' => 1,
                'type' => 2, // Description
                'dimension_id' => 0,
                'anchors' => serialize([]),
                'practice' => 0,
                'created_at' => Carbon::parse('2020-01-27T02:23:14.000000Z'),
                'updated_at' => Carbon::parse('2022-10-20T18:59:02.000000Z')
            ],
            // Creative Problem Solving - Rating
            [
                'content' => '<strong><big>Your rating for Creative Problem Solving:</big></strong>',
                'assessment_id' => $assessmentId,
                'number' => 1,
                'type' => 1, // Multiple Choice
                'dimension_id' => 1,
                'anchors' => serialize([
                    ['tag' => 'Below Expectations', 'value' => '1'],
                    ['tag' => 'Slightly Below Expectations', 'value' => '2'],
                    ['tag' => 'Meets Expectations', 'value' => '3'],
                    ['tag' => 'Slightly Exceeds Expectations', 'value' => '4'],
                    ['tag' => 'Exceeds Expectations', 'value' => '5']
                ]),
                'practice' => 0,
                'created_at' => Carbon::parse('2020-01-27T02:23:14.000000Z'),
                'updated_at' => Carbon::parse('2022-10-20T18:59:02.000000Z')
            ],
            // Creative Problem Solving - Descriptors
            [
                'content' => '<table border="0" cellpadding="0" cellspacing="0" style="width:100.0%" class=" cke_show_border"><tbody><tr><td><p><br></p><p>Lacks creativity when trying to solve existing problems</p></td><td><br></td><td><p>Sometimes develops creative solutions to navigate problems</p></td><td><br></td><td><p>Always seeks innovative solutions to problems</p></td></tr><tr><td><p>Consistently fails to utilize talent to improve processes, advance procedures, or execute tasks</p></td><td><br></td><td><p>Implements novel solutions to existing problems with some success</p></td><td><br></td><td><p>Consistently implements creative solutions to existing challenges with high success</p></td></tr><tr><td><p>Fails to follow through with executing new ideas/initiatives &nbsp;as promised</p></td><td><br></td><td><p>Notices when change is necessary</p></td><td><br></td><td><p>Implements new/improved processes or procedures to navigate obstacles</p></td></tr><tr><td><p>Typically satisfied with following the same routines/processes over extended periods of time</p></td><td><br></td><td><br></td><td><br></td><td><p>Consistently educates self in areas of expertise and seeks out new knowledge in unfamiliar areas</p></td></tr><tr><td><p>Highly resistant to change</p></td><td><br></td><td><br></td><td><br></td><td><p>Takes positive action, even in the face of unforeseen obstacles, and works to overcome traditional boundaries to move ahead in day-to-day activities as well as long-term activities.&nbsp;</p></td></tr></tbody></table>',
                'assessment_id' => $assessmentId,
                'number' => 2,
                'type' => 2, // Description
                'dimension_id' => 0,
                'anchors' => serialize([]),
                'practice' => 0,
                'created_at' => Carbon::parse('2020-01-27T02:23:14.000000Z'),
                'updated_at' => Carbon::parse('2022-10-20T18:59:02.000000Z')
            ],
            // Creative Problem Solving - Comments
            [
                'content' => '<strong><big>Developmental Comments for Creative Problem Solving:</big></strong>',
                'assessment_id' => $assessmentId,
                'number' => 2,
                'type' => 3, // Text Input
                'dimension_id' => 1,
                'anchors' => serialize([]),
                'practice' => 0,
                'created_at' => Carbon::parse('2020-01-26T02:26:50.000000Z'),
                'updated_at' => Carbon::parse('2022-10-20T18:59:02.000000Z')
            ],
            // Leadership Adaptability - Instructions
            [
                'content' => '<h2>Leadership Adaptability</h2><p><strong>Definition</strong>: Leadership Adaptability is having the ability to see the need for change early on. Having the willingness to smoothly and comfortably adjust his/her work style to the change as well as assist his/her team in positively adapting to the change. This competency also captures one\'s psychological ownership over the change.</p><h3 style="color:rgb(170, 170, 170); font-style:italic"><small><big>Please select your rating for [name] for this dimension.</big></small></h3>',
                'assessment_id' => $assessmentId,
                'number' => 3,
                'type' => 2, // Description
                'dimension_id' => 0,
                'anchors' => serialize([]),
                'practice' => 0,
                'created_at' => Carbon::parse('2020-01-27T22:13:59.000000Z'),
                'updated_at' => Carbon::parse('2022-10-20T18:59:02.000000Z')
            ],
            // Leadership Adaptability - Rating
            [
                'content' => '<strong><big>Your rating for Leadership Adaptability:</big></strong>',
                'assessment_id' => $assessmentId,
                'number' => 3,
                'type' => 1, // Multiple Choice
                'dimension_id' => 2,
                'anchors' => serialize([
                    ['tag' => 'Below Expectations', 'value' => '1'],
                    ['tag' => 'Slightly Below Expectations', 'value' => '2'],
                    ['tag' => 'Meets Expectations', 'value' => '3'],
                    ['tag' => 'Slightly Exceeds Expectations', 'value' => '4'],
                    ['tag' => 'Exceeds Expectations', 'value' => '5']
                ]),
                'practice' => 0,
                'created_at' => Carbon::parse('2020-01-27T22:13:59.000000Z'),
                'updated_at' => Carbon::parse('2022-10-20T18:59:02.000000Z')
            ],
            // Leadership Adaptability - Descriptors
            [
                'content' => '<table border="0" cellpadding="0" cellspacing="0" style="width:100.0%" class=" cke_show_border"><tbody><tr><td><p>Fails to make changes happen even after being asked to do so by trusted advisors or superiors</p></td><td><br></td><td><p>Makes changes happen without significant negative impact when asked to do so&nbsp;</p></td><td><br></td><td><p>Takes ownership of changes and creates a positive change atmosphere</p></td></tr><tr><td><p>Does not react well to changing environments and fails to maneuver him/herself in a manner that generates productive outcomes</p></td><td><br></td><td><p>Reacts well to changing environments and minimizes stress on others</p></td><td><br></td><td><p>Has the ability to see change coming and actively maneuvers him/herself to create a positive outcome</p></td></tr><tr><td><p>Handles change poorly, creating a negative environment for others</p></td><td><br></td><td><p>Generally supports change</p></td><td><br></td><td><p>Acts as an advocate for change to others facing difficult circumstances</p></td></tr><tr><td><br></td><td><br></td><td><p>Handles change well without creating a negative environment for others</p></td><td><br></td><td><p>Volunteers to be an active participant in groups or tasks where change is expected</p></td></tr></tbody></table>',
                'assessment_id' => $assessmentId,
                'number' => 4,
                'type' => 2, // Description
                'dimension_id' => 0,
                'anchors' => serialize([]),
                'practice' => 0,
                'created_at' => Carbon::parse('2020-01-27T22:13:59.000000Z'),
                'updated_at' => Carbon::parse('2022-10-20T18:59:02.000000Z')
            ],
            // Leadership Adaptability - Comments
            [
                'content' => '<strong><big>Developmental Comments for Leadership Adaptability:</big></strong>',
                'assessment_id' => $assessmentId,
                'number' => 4,
                'type' => 3, // Text Input
                'dimension_id' => 2,
                'anchors' => serialize([]),
                'practice' => 0,
                'created_at' => Carbon::parse('2020-01-27T23:03:37.000000Z'),
                'updated_at' => Carbon::parse('2022-10-20T18:59:02.000000Z')
            ],
            // Collaboration - Instructions
            [
                'content' => '<h2>Collaboration</h2><p><strong>Definition</strong>: Collaboration is being able to effectively work with internal stakeholders/employees up and down the chain of organizational hierarchy (vertical) and horizontally with peers as well as external individuals/organizations/partners (e.g., vendor, community leaders) all the while&nbsp;knowing and showing ones authentic self to others at work while simultaneously knowing how one is portrayed across all work settings.&nbsp;</p><h3 style="color:rgb(170, 170, 170); font-style:italic"><small><big>Please select your rating for [name] for this dimension.</big></small></h3>',
                'assessment_id' => $assessmentId,
                'number' => 5,
                'type' => 2, // Description
                'dimension_id' => 0,
                'anchors' => serialize([]),
                'practice' => 0,
                'created_at' => Carbon::parse('2020-01-27T23:21:20.000000Z'),
                'updated_at' => Carbon::parse('2022-10-20T18:59:02.000000Z')
            ],
            // Collaboration - Rating
            [
                'content' => '<big><strong>Your rating for Collaboration:</strong></big>',
                'assessment_id' => $assessmentId,
                'number' => 5,
                'type' => 1, // Multiple Choice
                'dimension_id' => 3,
                'anchors' => serialize([
                    ['tag' => 'Below Expectations', 'value' => '1'],
                    ['tag' => 'Slightly Below Expectations', 'value' => '2'],
                    ['tag' => 'Meets Expectations', 'value' => '3'],
                    ['tag' => 'Slightly Exceeds Expectations', 'value' => '4'],
                    ['tag' => 'Exceeds Expectations', 'value' => '5']
                ]),
                'practice' => 0,
                'created_at' => Carbon::parse('2020-01-27T23:21:20.000000Z'),
                'updated_at' => Carbon::parse('2022-10-20T18:59:02.000000Z')
            ]
        ];

        // Add more questions for remaining dimensions (simplified for brevity)
        $dimensionNames = ['Self-Development', 'Performance Management', 'Business Mindset', 'Customer Focus', 'Communication', 'Ethics & Integrity'];
        $dimensionIds = [4, 5, 6, 7, 8, 9];
        
        for ($i = 0; $i < count($dimensionNames); $i++) {
            $questionNumber = 6 + ($i * 4);
            $dimensionId = $dimensionIds[$i];
            
            // Instructions
            $questions[] = [
                'content' => '<h2>' . $dimensionNames[$i] . '</h2><p><strong>Definition</strong>: [Definition for ' . $dimensionNames[$i] . ']</p><h3 style="color:rgb(170, 170, 170); font-style:italic"><small><big>Please select your rating for [name] for this dimension.</big></small></h3>',
                'assessment_id' => $assessmentId,
                'number' => $questionNumber,
                'type' => 2, // Description
                'dimension_id' => 0,
                'anchors' => serialize([]),
                'practice' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
            
            // Rating
            $questions[] = [
                'content' => '<strong><big>Your rating for ' . $dimensionNames[$i] . ':</big></strong>',
                'assessment_id' => $assessmentId,
                'number' => $questionNumber,
                'type' => 1, // Multiple Choice
                'dimension_id' => $dimensionId,
                'anchors' => serialize([
                    ['tag' => 'Below Expectations', 'value' => '1'],
                    ['tag' => 'Slightly Below Expectations', 'value' => '2'],
                    ['tag' => 'Meets Expectations', 'value' => '3'],
                    ['tag' => 'Slightly Exceeds Expectations', 'value' => '4'],
                    ['tag' => 'Exceeds Expectations', 'value' => '5']
                ]),
                'practice' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
            
            // Descriptors (simplified)
            $questions[] = [
                'content' => '<p>Performance descriptors for ' . $dimensionNames[$i] . ' would go here.</p>',
                'assessment_id' => $assessmentId,
                'number' => $questionNumber + 1,
                'type' => 2, // Description
                'dimension_id' => 0,
                'anchors' => serialize([]),
                'practice' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
            
            // Comments
            $questions[] = [
                'content' => '<strong><big>Developmental Comments for ' . $dimensionNames[$i] . ':</big></strong>',
                'assessment_id' => $assessmentId,
                'number' => $questionNumber + 1,
                'type' => 3, // Text Input
                'dimension_id' => $dimensionId,
                'anchors' => serialize([]),
                'practice' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }

        foreach ($questions as $question) {
            DB::table('questions')->insert($question);
        }

        $this->command->info('Created ' . count($questions) . ' questions');
    }

    /**
     * Create industry benchmarks for the dimensions
     */
    private function createIndustryBenchmarks()
    {
        // First, ensure we have some basic industries
        $this->createBasicIndustries();
        
        $benchmarks = [
            // Creative Problem Solving benchmarks
            ['industry_id' => 1, 'dimension_id' => 1, 'value' => 3.85],
            ['industry_id' => 2, 'dimension_id' => 1, 'value' => 3.92],
            ['industry_id' => 3, 'dimension_id' => 1, 'value' => 3.78],
            ['industry_id' => 4, 'dimension_id' => 1, 'value' => 3.89],
            ['industry_id' => 5, 'dimension_id' => 1, 'value' => 3.95],
            
            // Leadership Adaptability benchmarks
            ['industry_id' => 1, 'dimension_id' => 2, 'value' => 3.76],
            ['industry_id' => 2, 'dimension_id' => 2, 'value' => 3.83],
            ['industry_id' => 3, 'dimension_id' => 2, 'value' => 3.71],
            ['industry_id' => 4, 'dimension_id' => 2, 'value' => 3.81],
            ['industry_id' => 5, 'dimension_id' => 2, 'value' => 3.88],
            
            // Collaboration benchmarks
            ['industry_id' => 1, 'dimension_id' => 3, 'value' => 4.12],
            ['industry_id' => 2, 'dimension_id' => 3, 'value' => 4.18],
            ['industry_id' => 3, 'dimension_id' => 3, 'value' => 4.05],
            ['industry_id' => 4, 'dimension_id' => 3, 'value' => 4.15],
            ['industry_id' => 5, 'dimension_id' => 3, 'value' => 4.22],
            
            // Self-Development benchmarks
            ['industry_id' => 1, 'dimension_id' => 4, 'value' => 3.68],
            ['industry_id' => 2, 'dimension_id' => 4, 'value' => 3.75],
            ['industry_id' => 3, 'dimension_id' => 4, 'value' => 3.62],
            ['industry_id' => 4, 'dimension_id' => 4, 'value' => 3.72],
            ['industry_id' => 5, 'dimension_id' => 4, 'value' => 3.79],
            
            // Performance Management benchmarks
            ['industry_id' => 1, 'dimension_id' => 5, 'value' => 3.91],
            ['industry_id' => 2, 'dimension_id' => 5, 'value' => 3.98],
            ['industry_id' => 3, 'dimension_id' => 5, 'value' => 3.85],
            ['industry_id' => 4, 'dimension_id' => 5, 'value' => 3.94],
            ['industry_id' => 5, 'dimension_id' => 5, 'value' => 4.01],
            
            // Business Mindset benchmarks
            ['industry_id' => 1, 'dimension_id' => 6, 'value' => 3.73],
            ['industry_id' => 2, 'dimension_id' => 6, 'value' => 3.80],
            ['industry_id' => 3, 'dimension_id' => 6, 'value' => 3.67],
            ['industry_id' => 4, 'dimension_id' => 6, 'value' => 3.77],
            ['industry_id' => 5, 'dimension_id' => 6, 'value' => 3.84],
            
            // Customer Focus benchmarks
            ['industry_id' => 1, 'dimension_id' => 7, 'value' => 4.15],
            ['industry_id' => 2, 'dimension_id' => 7, 'value' => 4.21],
            ['industry_id' => 3, 'dimension_id' => 7, 'value' => 4.08],
            ['industry_id' => 4, 'dimension_id' => 7, 'value' => 4.18],
            ['industry_id' => 5, 'dimension_id' => 7, 'value' => 4.25],
            
            // Communication benchmarks
            ['industry_id' => 1, 'dimension_id' => 8, 'value' => 3.94],
            ['industry_id' => 2, 'dimension_id' => 8, 'value' => 4.01],
            ['industry_id' => 3, 'dimension_id' => 8, 'value' => 3.88],
            ['industry_id' => 4, 'dimension_id' => 8, 'value' => 3.97],
            ['industry_id' => 5, 'dimension_id' => 8, 'value' => 4.04],
            
            // Ethics & Integrity benchmarks
            ['industry_id' => 1, 'dimension_id' => 9, 'value' => 4.32],
            ['industry_id' => 2, 'dimension_id' => 9, 'value' => 4.38],
            ['industry_id' => 3, 'dimension_id' => 9, 'value' => 4.25],
            ['industry_id' => 4, 'dimension_id' => 9, 'value' => 4.35],
            ['industry_id' => 5, 'dimension_id' => 9, 'value' => 4.42]
        ];

        foreach ($benchmarks as $benchmark) {
            DB::table('benchmarks')->insert([
                'industry_id' => $benchmark['industry_id'],
                'dimension_id' => $benchmark['dimension_id'],
                'value' => $benchmark['value'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }

        $this->command->info('Created ' . count($benchmarks) . ' industry benchmarks');
    }

    /**
     * Create basic industries if they don't exist
     */
    private function createBasicIndustries()
    {
        $industries = [
            ['name' => 'Technology'],
            ['name' => 'Healthcare'],
            ['name' => 'Finance'],
            ['name' => 'Manufacturing'],
            ['name' => 'Education'],
            ['name' => 'Retail'],
            ['name' => 'Consulting'],
            ['name' => 'Non-Profit'],
            ['name' => 'Government'],
            ['name' => 'Energy'],
            ['name' => 'Transportation'],
            ['name' => 'Real Estate'],
            ['name' => 'Media'],
            ['name' => 'Hospitality'],
            ['name' => 'Utilities'],
            ['name' => 'Transportation & Warehouse']
        ];

        foreach ($industries as $industry) {
            if (!DB::table('industries')->where('name', $industry['name'])->exists()) {
                DB::table('industries')->insert([
                    'name' => $industry['name'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
        }
    }
}
