<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvolvedLeaderAssessmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Seeding Involved-Leader Assessment...');

        // Create the assessment
        $assessmentId = $this->createAssessment();
        
        // Create dimensions
        $dimensionIds = $this->createDimensions($assessmentId);
        
        // Create questions
        $this->createQuestions($assessmentId, $dimensionIds);
        
        // Create industry benchmarks
        $this->createIndustryBenchmarks($dimensionIds);
        
        $this->command->info('Involved-Leader Assessment seeded successfully!');
    }

    /**
     * Create the main assessment record
     */
    private function createAssessment()
    {
        $assessmentId = DB::table('assessments')->insertGetId([
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
            'custom_fields' => serialize([
                'tag' => ['name', 'email'],
                'default' => ['', '']
            ]),
            'target' => 1,
            'last_modified' => Carbon::parse('2020-04-02T07:48:24.000000Z'),
            'created_at' => Carbon::parse('2020-04-02T07:48:24.000000Z'),
            'updated_at' => Carbon::parse('2021-05-06T15:08:29.000000Z'),
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
                'name' => 'Relationships',
                'parent' => 0,
                'code' => 'Rel',
                'assessment_id' => $assessmentId,
                'created_at' => Carbon::parse('2020-04-02T09:09:54.000000Z'),
                'updated_at' => Carbon::parse('2021-06-08T23:21:17.000000Z')
            ],
            [
                'name' => 'Servitude',
                'parent' => 0,
                'code' => 'Serv',
                'assessment_id' => $assessmentId,
                'created_at' => Carbon::parse('2020-04-02T09:10:44.000000Z'),
                'updated_at' => Carbon::parse('2020-04-11T09:42:33.000000Z')
            ],
            [
                'name' => 'Ethical',
                'parent' => 0,
                'code' => 'Ethic',
                'assessment_id' => $assessmentId,
                'created_at' => Carbon::parse('2020-04-02T09:11:21.000000Z'),
                'updated_at' => Carbon::parse('2020-04-11T09:42:48.000000Z')
            ],
            [
                'name' => 'Analytical',
                'parent' => 0,
                'code' => 'Analytic',
                'assessment_id' => $assessmentId,
                'created_at' => Carbon::parse('2020-04-02T09:11:41.000000Z'),
                'updated_at' => Carbon::parse('2020-04-11T09:42:53.000000Z')
            ],
            [
                'name' => 'Conflict',
                'parent' => 0,
                'code' => 'Con',
                'assessment_id' => $assessmentId,
                'created_at' => Carbon::parse('2020-04-02T09:12:03.000000Z'),
                'updated_at' => Carbon::parse('2020-04-11T09:43:05.000000Z')
            ],
            [
                'name' => 'Communication',
                'parent' => 0,
                'code' => 'COMM',
                'assessment_id' => $assessmentId,
                'created_at' => Carbon::parse('2020-04-11T10:11:09.000000Z'),
                'updated_at' => Carbon::parse('2020-04-11T10:11:09.000000Z')
            ],
            [
                'name' => 'Rewards',
                'parent' => 0,
                'code' => 'Rew',
                'assessment_id' => $assessmentId,
                'created_at' => Carbon::parse('2020-04-11T10:11:44.000000Z'),
                'updated_at' => Carbon::parse('2020-04-11T10:11:44.000000Z')
            ]
        ];

        $dimensionIds = [];
        foreach ($dimensions as $dimension) {
            $id = DB::table('dimensions')->insertGetId($dimension);
            $dimensionIds[$dimension['code']] = $id;
        }

        $this->command->info('Created ' . count($dimensions) . ' dimensions');
        return $dimensionIds;
    }

    /**
     * Create the assessment questions
     */
    private function createQuestions($assessmentId, $dimensionIds)
    {
        $questions = [
            // Relationships dimension questions
            [
                'content' => '[name] encourages me and my teammates to express our ideas',
                'assessment_id' => $assessmentId,
                'number' => 1,
                'type' => 1, // Multiple Choice
                'dimension_id' => $dimensionIds['Rel'],
                'anchors' => serialize([
                    ['tag' => 'Rarely', 'value' => '1'],
                    ['tag' => 'Sometimes', 'value' => '2'],
                    ['tag' => 'Most of the Time', 'value' => '3'],
                    ['tag' => 'Almost All of the Time', 'value' => '4'],
                    ['tag' => 'Always', 'value' => '5']
                ]),
                'practice' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'content' => '[name] incorporates our ideas/suggestions when making decisions',
                'assessment_id' => $assessmentId,
                'number' => 2,
                'type' => 1,
                'dimension_id' => $dimensionIds['Rel'],
                'anchors' => serialize([
                    ['tag' => 'Rarely', 'value' => '1'],
                    ['tag' => 'Sometimes', 'value' => '2'],
                    ['tag' => 'Most of the Time', 'value' => '3'],
                    ['tag' => 'Almost All of the Time', 'value' => '4'],
                    ['tag' => 'Always', 'value' => '5']
                ]),
                'practice' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'content' => '[name] consults with me and my teammates when making decisions',
                'assessment_id' => $assessmentId,
                'number' => 3,
                'type' => 1,
                'dimension_id' => $dimensionIds['Rel'],
                'anchors' => serialize([
                    ['tag' => 'Rarely', 'value' => '1'],
                    ['tag' => 'Sometimes', 'value' => '2'],
                    ['tag' => 'Most of the Time', 'value' => '3'],
                    ['tag' => 'Almost All of the Time', 'value' => '4'],
                    ['tag' => 'Always', 'value' => '5']
                ]),
                'practice' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'content' => '[name] encourages me and my teammates to assist in decision-making',
                'assessment_id' => $assessmentId,
                'number' => 4,
                'type' => 1,
                'dimension_id' => $dimensionIds['Rel'],
                'anchors' => serialize([
                    ['tag' => 'Rarely', 'value' => '1'],
                    ['tag' => 'Sometimes', 'value' => '2'],
                    ['tag' => 'Most of the Time', 'value' => '3'],
                    ['tag' => 'Almost All of the Time', 'value' => '4'],
                    ['tag' => 'Always', 'value' => '5']
                ]),
                'practice' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Servitude dimension questions
            [
                'content' => '[name] helps me and my teammates understand the meaningfulness of our work',
                'assessment_id' => $assessmentId,
                'number' => 5,
                'type' => 1,
                'dimension_id' => $dimensionIds['Serv'],
                'anchors' => serialize([
                    ['tag' => 'Rarely', 'value' => '1'],
                    ['tag' => 'Sometimes', 'value' => '2'],
                    ['tag' => 'Most of the Time', 'value' => '3'],
                    ['tag' => 'Almost All of the Time', 'value' => '4'],
                    ['tag' => 'Always', 'value' => '5']
                ]),
                'practice' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'content' => '[name] helps me and my teammates understand how our objectives/goals align with the entire organization',
                'assessment_id' => $assessmentId,
                'number' => 6,
                'type' => 1,
                'dimension_id' => $dimensionIds['Serv'],
                'anchors' => serialize([
                    ['tag' => 'Rarely', 'value' => '1'],
                    ['tag' => 'Sometimes', 'value' => '2'],
                    ['tag' => 'Most of the Time', 'value' => '3'],
                    ['tag' => 'Almost All of the Time', 'value' => '4'],
                    ['tag' => 'Always', 'value' => '5']
                ]),
                'practice' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'content' => '[name] helps me and my teammates understand the positive impact we have on all stakeholders',
                'assessment_id' => $assessmentId,
                'number' => 7,
                'type' => 1,
                'dimension_id' => $dimensionIds['Serv'],
                'anchors' => serialize([
                    ['tag' => 'Rarely', 'value' => '1'],
                    ['tag' => 'Sometimes', 'value' => '2'],
                    ['tag' => 'Most of the Time', 'value' => '3'],
                    ['tag' => 'Almost All of the Time', 'value' => '4'],
                    ['tag' => 'Always', 'value' => '5']
                ]),
                'practice' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'content' => '[name] helps others generate a sense of meaning out of everyday life at work',
                'assessment_id' => $assessmentId,
                'number' => 8,
                'type' => 1,
                'dimension_id' => $dimensionIds['Serv'],
                'anchors' => serialize([
                    ['tag' => 'Rarely', 'value' => '1'],
                    ['tag' => 'Sometimes', 'value' => '2'],
                    ['tag' => 'Most of the Time', 'value' => '3'],
                    ['tag' => 'Almost All of the Time', 'value' => '4'],
                    ['tag' => 'Always', 'value' => '5']
                ]),
                'practice' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Ethical dimension questions
            [
                'content' => '[name] demonstrates high ethical standards in all interactions',
                'assessment_id' => $assessmentId,
                'number' => 9,
                'type' => 1,
                'dimension_id' => $dimensionIds['Ethic'],
                'anchors' => serialize([
                    ['tag' => 'Rarely', 'value' => '1'],
                    ['tag' => 'Sometimes', 'value' => '2'],
                    ['tag' => 'Most of the Time', 'value' => '3'],
                    ['tag' => 'Almost All of the Time', 'value' => '4'],
                    ['tag' => 'Always', 'value' => '5']
                ]),
                'practice' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'content' => '[name] makes decisions based on what is right, not just what is expedient',
                'assessment_id' => $assessmentId,
                'number' => 10,
                'type' => 1,
                'dimension_id' => $dimensionIds['Ethic'],
                'anchors' => serialize([
                    ['tag' => 'Rarely', 'value' => '1'],
                    ['tag' => 'Sometimes', 'value' => '2'],
                    ['tag' => 'Most of the Time', 'value' => '3'],
                    ['tag' => 'Almost All of the Time', 'value' => '4'],
                    ['tag' => 'Always', 'value' => '5']
                ]),
                'practice' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Analytical dimension questions
            [
                'content' => '[name] analyzes problems thoroughly before making decisions',
                'assessment_id' => $assessmentId,
                'number' => 11,
                'type' => 1,
                'dimension_id' => $dimensionIds['Analytic'],
                'anchors' => serialize([
                    ['tag' => 'Rarely', 'value' => '1'],
                    ['tag' => 'Sometimes', 'value' => '2'],
                    ['tag' => 'Most of the Time', 'value' => '3'],
                    ['tag' => 'Almost All of the Time', 'value' => '4'],
                    ['tag' => 'Always', 'value' => '5']
                ]),
                'practice' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'content' => '[name] uses data and evidence to support decisions',
                'assessment_id' => $assessmentId,
                'number' => 12,
                'type' => 1,
                'dimension_id' => $dimensionIds['Analytic'],
                'anchors' => serialize([
                    ['tag' => 'Rarely', 'value' => '1'],
                    ['tag' => 'Sometimes', 'value' => '2'],
                    ['tag' => 'Most of the Time', 'value' => '3'],
                    ['tag' => 'Almost All of the Time', 'value' => '4'],
                    ['tag' => 'Always', 'value' => '5']
                ]),
                'practice' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Conflict dimension questions
            [
                'content' => '[name] addresses conflicts directly and constructively',
                'assessment_id' => $assessmentId,
                'number' => 13,
                'type' => 1,
                'dimension_id' => $dimensionIds['Con'],
                'anchors' => serialize([
                    ['tag' => 'Rarely', 'value' => '1'],
                    ['tag' => 'Sometimes', 'value' => '2'],
                    ['tag' => 'Most of the Time', 'value' => '3'],
                    ['tag' => 'Almost All of the Time', 'value' => '4'],
                    ['tag' => 'Always', 'value' => '5']
                ]),
                'practice' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'content' => '[name] helps resolve disagreements between team members',
                'assessment_id' => $assessmentId,
                'number' => 14,
                'type' => 1,
                'dimension_id' => $dimensionIds['Con'],
                'anchors' => serialize([
                    ['tag' => 'Rarely', 'value' => '1'],
                    ['tag' => 'Sometimes', 'value' => '2'],
                    ['tag' => 'Most of the Time', 'value' => '3'],
                    ['tag' => 'Almost All of the Time', 'value' => '4'],
                    ['tag' => 'Always', 'value' => '5']
                ]),
                'practice' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Communication dimension questions
            [
                'content' => '[name] communicates clearly and effectively with the team',
                'assessment_id' => $assessmentId,
                'number' => 15,
                'type' => 1,
                'dimension_id' => $dimensionIds['COMM'],
                'anchors' => serialize([
                    ['tag' => 'Rarely', 'value' => '1'],
                    ['tag' => 'Sometimes', 'value' => '2'],
                    ['tag' => 'Most of the Time', 'value' => '3'],
                    ['tag' => 'Almost All of the Time', 'value' => '4'],
                    ['tag' => 'Always', 'value' => '5']
                ]),
                'practice' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'content' => '[name] listens actively to team members\' concerns and ideas',
                'assessment_id' => $assessmentId,
                'number' => 16,
                'type' => 1,
                'dimension_id' => $dimensionIds['COMM'],
                'anchors' => serialize([
                    ['tag' => 'Rarely', 'value' => '1'],
                    ['tag' => 'Sometimes', 'value' => '2'],
                    ['tag' => 'Most of the Time', 'value' => '3'],
                    ['tag' => 'Almost All of the Time', 'value' => '4'],
                    ['tag' => 'Always', 'value' => '5']
                ]),
                'practice' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Rewards dimension questions
            [
                'content' => '[name] recognizes and rewards good performance appropriately',
                'assessment_id' => $assessmentId,
                'number' => 17,
                'type' => 1,
                'dimension_id' => $dimensionIds['Rew'],
                'anchors' => serialize([
                    ['tag' => 'Rarely', 'value' => '1'],
                    ['tag' => 'Sometimes', 'value' => '2'],
                    ['tag' => 'Most of the Time', 'value' => '3'],
                    ['tag' => 'Almost All of the Time', 'value' => '4'],
                    ['tag' => 'Always', 'value' => '5']
                ]),
                'practice' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'content' => '[name] provides meaningful feedback to help team members improve',
                'assessment_id' => $assessmentId,
                'number' => 18,
                'type' => 1,
                'dimension_id' => $dimensionIds['Rew'],
                'anchors' => serialize([
                    ['tag' => 'Rarely', 'value' => '1'],
                    ['tag' => 'Sometimes', 'value' => '2'],
                    ['tag' => 'Most of the Time', 'value' => '3'],
                    ['tag' => 'Almost All of the Time', 'value' => '4'],
                    ['tag' => 'Always', 'value' => '5']
                ]),
                'practice' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ];

        foreach ($questions as $question) {
            DB::table('questions')->insert($question);
        }

        $this->command->info('Created ' . count($questions) . ' questions');
    }

    /**
     * Create industry benchmarks for the dimensions
     */
    private function createIndustryBenchmarks($dimensionIds)
    {
        // First, ensure we have some basic industries
        $this->createBasicIndustries();
        
        $benchmarks = [];
        
        // Create benchmarks for each dimension across different industries
        $industries = [1, 2, 3, 4, 5]; // Technology, Healthcare, Finance, Manufacturing, Education
        
        foreach ($dimensionIds as $code => $dimensionId) {
            foreach ($industries as $industryId) {
                // Generate realistic benchmark values for leadership dimensions
                $baseValue = $this->getBaseBenchmarkValue($code);
                $variation = rand(-15, 15) / 100; // ±15% variation
                $value = round($baseValue * (1 + $variation), 2);
                
                $benchmarks[] = [
                    'industry_id' => $industryId,
                    'dimension_id' => $dimensionId,
                    'value' => $value,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }
        }

        foreach ($benchmarks as $benchmark) {
            DB::table('benchmarks')->insert($benchmark);
        }

        $this->command->info('Created ' . count($benchmarks) . ' industry benchmarks');
    }

    /**
     * Get base benchmark value for each dimension
     */
    private function getBaseBenchmarkValue($code)
    {
        $baseValues = [
            'Rel' => 3.8,      // Relationships
            'Serv' => 3.6,     // Servitude
            'Ethic' => 4.2,    // Ethical
            'Analytic' => 3.7, // Analytical
            'Con' => 3.5,      // Conflict
            'COMM' => 3.9,     // Communication
            'Rew' => 3.4       // Rewards
        ];
        
        return $baseValues[$code] ?? 3.7;
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
