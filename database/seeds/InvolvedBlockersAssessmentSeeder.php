<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvolvedBlockersAssessmentSeeder extends Seeder
{
    public function run()
    {
        echo "Seeding Involved-Blockers Assessment...\n";
        
        // Create the assessment
        $assessmentId = $this->createAssessment();
        
        // Create dimensions
        $dimensionMap = $this->createDimensions($assessmentId);
        
        // Create questions
        $this->createQuestions($assessmentId, $dimensionMap);
        
        // Create industry benchmarks
        $this->createIndustryBenchmarks($dimensionMap);
        
        echo "Involved-Blockers Assessment seeded successfully!\n";
    }
    
    private function createAssessment()
    {
        // Get the first user ID (admin user)
        $userId = DB::table('users')->first()->id;
        
        $assessmentId = DB::table('assessments')->insertGetId([
            'user_id' => $userId,
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
            'custom_fields' => null,
            'target' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'last_modified' => Carbon::now()
        ]);
        
        echo "Created assessment with ID: {$assessmentId}\n";
        return $assessmentId;
    }
    
    private function createDimensions($assessmentId)
    {
        $dimensions = [
            ['name' => 'Fear', 'code' => 'Fear'],
            ['name' => 'Willfulness', 'code' => 'Will'],
            ['name' => 'Justification', 'code' => 'Just'],
            ['name' => 'Victimization', 'code' => 'Vict'],
            ['name' => 'Entitlement', 'code' => 'Ent']
        ];
        
        $dimensionMap = [];
        foreach ($dimensions as $dimension) {
            $dimensionId = DB::table('dimensions')->insertGetId([
                'name' => $dimension['name'],
                'code' => $dimension['code'],
                'assessment_id' => $assessmentId,
                'parent' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            
            $dimensionMap[$dimension['code']] = $dimensionId;
        }
        
        echo "Created " . count($dimensions) . " dimensions\n";
        return $dimensionMap;
    }
    
    private function createQuestions($assessmentId, $dimensionMap)
    {
        $questions = [
            // Fear dimension (10 questions)
            ['number' => 1, 'content' => 'I worry about unknown situations or tasks', 'dimension' => 'Fear'],
            ['number' => 2, 'content' => 'I worry that I will do the wrong things at work.', 'dimension' => 'Fear'],
            ['number' => 3, 'content' => 'I am afraid that people will find fault with my work', 'dimension' => 'Fear'],
            ['number' => 4, 'content' => 'My friends have told me that I let fear get in my way sometimes', 'dimension' => 'Fear'],
            ['number' => 5, 'content' => 'I avoid situations where I might perform poorly', 'dimension' => 'Fear'],
            ['number' => 6, 'content' => 'I am afraid that people will find fault with my performance', 'dimension' => 'Fear'],
            ['number' => 7, 'content' => 'Fear can sometimes slow me down', 'dimension' => 'Fear'],
            ['number' => 8, 'content' => 'I rarely worry about what kind of impression I make at work', 'dimension' => 'Fear', 'reverse' => true],
            ['number' => 9, 'content' => 'I am generally courageous and comfortable when facing obstacles', 'dimension' => 'Fear', 'reverse' => true],
            ['number' => 10, 'content' => 'I prefer to learn from new work tasks than let fear of failure slow me down', 'dimension' => 'Fear', 'reverse' => true],
            
            // Willfulness dimension (7 questions)
            ['number' => 11, 'content' => 'I usually know more than other people in the room', 'dimension' => 'Will'],
            ['number' => 12, 'content' => 'Given my background, I have more expertise than others I work with', 'dimension' => 'Will'],
            ['number' => 13, 'content' => 'My knowledge and expertise exceeds most other people I know', 'dimension' => 'Will'],
            ['number' => 14, 'content' => 'My friends tell me I am headstrong', 'dimension' => 'Will'],
            ['number' => 15, 'content' => 'Over the years, people have told me I am \'thick-headed\'', 'dimension' => 'Will'],
            ['number' => 16, 'content' => 'I have been known to plow ahead with plans, not taking into account other people\'s ideas/suggestions', 'dimension' => 'Will'],
            ['number' => 17, 'content' => 'More often than not, I am usually correct, and I let people know it', 'dimension' => 'Will'],
            
            // Justification dimension (7 questions)
            ['number' => 18, 'content' => 'Most things that go wrong are easily justifiable', 'dimension' => 'Just'],
            ['number' => 19, 'content' => 'I quickly rationalize negative events to make myself look/feel better', 'dimension' => 'Just'],
            ['number' => 20, 'content' => 'More often than not, I can easily pinpoint the reason something went wrong', 'dimension' => 'Just'],
            ['number' => 21, 'content' => 'When someone messes up, they usually justify their actions to save face', 'dimension' => 'Just'],
            ['number' => 22, 'content' => 'When bad things happen, it is because someone else messed up', 'dimension' => 'Just'],
            ['number' => 23, 'content' => 'Most people blame others when things go bad', 'dimension' => 'Just'],
            ['number' => 24, 'content' => 'I try to find the real reason for someone\'s success or failure', 'dimension' => 'Just', 'reverse' => true],
            
            // Victimization dimension (6 questions)
            ['number' => 25, 'content' => 'I am often blamed by others at work when things wrong', 'dimension' => 'Vict'],
            ['number' => 26, 'content' => 'Where I work, it is usually the messenger who is blamed, not the real culprit', 'dimension' => 'Vict'],
            ['number' => 27, 'content' => 'I have been the victim of too many issues at work', 'dimension' => 'Vict'],
            ['number' => 28, 'content' => 'I am often the victim, not the victorious, regardless of my role in a situation', 'dimension' => 'Vict'],
            ['number' => 29, 'content' => 'I feel that I am blamed when things go wrong, regardless of what I actually did', 'dimension' => 'Vict'],
            ['number' => 30, 'content' => 'There is always someone else to blame with things go wrong', 'dimension' => 'Vict', 'reverse' => true],
            
            // Entitlement dimension (7 questions)
            ['number' => 31, 'content' => 'Generally speaking, I\'m more deserving of good things', 'dimension' => 'Ent'],
            ['number' => 32, 'content' => 'I feel more entitled than most people', 'dimension' => 'Ent'],
            ['number' => 33, 'content' => 'If I were on a sinking ship, I should be one of the first people on a life boat', 'dimension' => 'Ent'],
            ['number' => 34, 'content' => 'Since I am worth it, I usually prefer the finer things in life', 'dimension' => 'Ent'],
            ['number' => 35, 'content' => 'I deserve the best', 'dimension' => 'Ent'],
            ['number' => 36, 'content' => 'People similar to me deserve special breaks', 'dimension' => 'Ent'],
            ['number' => 37, 'content' => 'I am more deserving of everything good in life', 'dimension' => 'Ent']
        ];
        
        foreach ($questions as $question) {
            $anchors = $this->getAnchors($question['reverse'] ?? false);
            
            DB::table('questions')->insert([
                'content' => $question['content'],
                'assessment_id' => $assessmentId,
                'number' => $question['number'],
                'type' => 1, // Multiple choice
                'dimension_id' => $dimensionMap[$question['dimension']],
                'anchors' => serialize($anchors),
                'practice' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
        
        echo "Created " . count($questions) . " questions\n";
    }
    
    private function getAnchors($reverse = false)
    {
        if ($reverse) {
            return [
                ['tag' => 'Strongly Disagree', 'value' => '5'],
                ['tag' => 'Disagree', 'value' => '4'],
                ['tag' => 'Neither Agree Nor Disagree', 'value' => '3'],
                ['tag' => 'Agree', 'value' => '2'],
                ['tag' => 'Strongly Agree', 'value' => '1']
            ];
        } else {
            return [
                ['tag' => 'Strongly Disagree', 'value' => '1'],
                ['tag' => 'Disagree', 'value' => '2'],
                ['tag' => 'Neither Agree Nor Disagree', 'value' => '3'],
                ['tag' => 'Agree', 'value' => '4'],
                ['tag' => 'Strongly Agree', 'value' => '5']
            ];
        }
    }
    
    private function createIndustryBenchmarks($dimensionMap)
    {
        // Ensure basic industries exist
        $this->createBasicIndustries();
        
        $benchmarks = [
            // Fear benchmarks (16 industries)
            ['industry_id' => 1, 'dimension_id' => $dimensionMap['Fear'], 'value' => 1.55],
            ['industry_id' => 2, 'dimension_id' => $dimensionMap['Fear'], 'value' => 1.35],
            ['industry_id' => 3, 'dimension_id' => $dimensionMap['Fear'], 'value' => 0.97],
            ['industry_id' => 4, 'dimension_id' => $dimensionMap['Fear'], 'value' => 1.36],
            ['industry_id' => 5, 'dimension_id' => $dimensionMap['Fear'], 'value' => 1.57],
            ['industry_id' => 6, 'dimension_id' => $dimensionMap['Fear'], 'value' => 1.44],
            ['industry_id' => 7, 'dimension_id' => $dimensionMap['Fear'], 'value' => 1.36],
            ['industry_id' => 8, 'dimension_id' => $dimensionMap['Fear'], 'value' => 1.54],
            ['industry_id' => 9, 'dimension_id' => $dimensionMap['Fear'], 'value' => 1.32],
            ['industry_id' => 10, 'dimension_id' => $dimensionMap['Fear'], 'value' => 1.6],
            ['industry_id' => 11, 'dimension_id' => $dimensionMap['Fear'], 'value' => 1.35],
            ['industry_id' => 12, 'dimension_id' => $dimensionMap['Fear'], 'value' => 1.33],
            ['industry_id' => 13, 'dimension_id' => $dimensionMap['Fear'], 'value' => 1.66],
            ['industry_id' => 14, 'dimension_id' => $dimensionMap['Fear'], 'value' => 1.5],
            ['industry_id' => 15, 'dimension_id' => $dimensionMap['Fear'], 'value' => 1.35],
            ['industry_id' => 16, 'dimension_id' => $dimensionMap['Fear'], 'value' => 1.39],
            
            // Willfulness benchmarks (16 industries)
            ['industry_id' => 1, 'dimension_id' => $dimensionMap['Will'], 'value' => 1.26],
            ['industry_id' => 2, 'dimension_id' => $dimensionMap['Will'], 'value' => 1.25],
            ['industry_id' => 3, 'dimension_id' => $dimensionMap['Will'], 'value' => 1.13],
            ['industry_id' => 4, 'dimension_id' => $dimensionMap['Will'], 'value' => 1.33],
            ['industry_id' => 5, 'dimension_id' => $dimensionMap['Will'], 'value' => 1.3],
            ['industry_id' => 6, 'dimension_id' => $dimensionMap['Will'], 'value' => 1.2],
            ['industry_id' => 7, 'dimension_id' => $dimensionMap['Will'], 'value' => 1.88],
            ['industry_id' => 8, 'dimension_id' => $dimensionMap['Will'], 'value' => 1.64],
            ['industry_id' => 9, 'dimension_id' => $dimensionMap['Will'], 'value' => 1.37],
            ['industry_id' => 10, 'dimension_id' => $dimensionMap['Will'], 'value' => 1.71],
            ['industry_id' => 11, 'dimension_id' => $dimensionMap['Will'], 'value' => 1.24],
            ['industry_id' => 12, 'dimension_id' => $dimensionMap['Will'], 'value' => 1.3],
            ['industry_id' => 13, 'dimension_id' => $dimensionMap['Will'], 'value' => 1.67],
            ['industry_id' => 14, 'dimension_id' => $dimensionMap['Will'], 'value' => 1.4],
            ['industry_id' => 15, 'dimension_id' => $dimensionMap['Will'], 'value' => 1.29],
            ['industry_id' => 16, 'dimension_id' => $dimensionMap['Will'], 'value' => 1.45],
            
            // Justification benchmarks (16 industries)
            ['industry_id' => 1, 'dimension_id' => $dimensionMap['Just'], 'value' => 1.25],
            ['industry_id' => 2, 'dimension_id' => $dimensionMap['Just'], 'value' => 1.47],
            ['industry_id' => 3, 'dimension_id' => $dimensionMap['Just'], 'value' => 1.66],
            ['industry_id' => 4, 'dimension_id' => $dimensionMap['Just'], 'value' => 1.4],
            ['industry_id' => 5, 'dimension_id' => $dimensionMap['Just'], 'value' => 1.32],
            ['industry_id' => 6, 'dimension_id' => $dimensionMap['Just'], 'value' => 1.3],
            ['industry_id' => 7, 'dimension_id' => $dimensionMap['Just'], 'value' => 1.31],
            ['industry_id' => 8, 'dimension_id' => $dimensionMap['Just'], 'value' => 1.62],
            ['industry_id' => 9, 'dimension_id' => $dimensionMap['Just'], 'value' => 1.4],
            ['industry_id' => 10, 'dimension_id' => $dimensionMap['Just'], 'value' => 1.47],
            ['industry_id' => 11, 'dimension_id' => $dimensionMap['Just'], 'value' => 1.4],
            ['industry_id' => 12, 'dimension_id' => $dimensionMap['Just'], 'value' => 1.81],
            ['industry_id' => 13, 'dimension_id' => $dimensionMap['Just'], 'value' => 1.42],
            ['industry_id' => 14, 'dimension_id' => $dimensionMap['Just'], 'value' => 1.35],
            ['industry_id' => 15, 'dimension_id' => $dimensionMap['Just'], 'value' => 1.64],
            ['industry_id' => 16, 'dimension_id' => $dimensionMap['Just'], 'value' => 1.61],
            
            // Victimization benchmarks (16 industries)
            ['industry_id' => 1, 'dimension_id' => $dimensionMap['Vict'], 'value' => 1.52],
            ['industry_id' => 2, 'dimension_id' => $dimensionMap['Vict'], 'value' => 1.39],
            ['industry_id' => 3, 'dimension_id' => $dimensionMap['Vict'], 'value' => 1.3],
            ['industry_id' => 4, 'dimension_id' => $dimensionMap['Vict'], 'value' => 1.19],
            ['industry_id' => 5, 'dimension_id' => $dimensionMap['Vict'], 'value' => 1.23],
            ['industry_id' => 6, 'dimension_id' => $dimensionMap['Vict'], 'value' => 1.51],
            ['industry_id' => 7, 'dimension_id' => $dimensionMap['Vict'], 'value' => 1.46],
            ['industry_id' => 8, 'dimension_id' => $dimensionMap['Vict'], 'value' => 1.44],
            ['industry_id' => 9, 'dimension_id' => $dimensionMap['Vict'], 'value' => 1.54],
            ['industry_id' => 10, 'dimension_id' => $dimensionMap['Vict'], 'value' => 1.53],
            ['industry_id' => 11, 'dimension_id' => $dimensionMap['Vict'], 'value' => 2.0],
            ['industry_id' => 12, 'dimension_id' => $dimensionMap['Vict'], 'value' => 1.46],
            ['industry_id' => 13, 'dimension_id' => $dimensionMap['Vict'], 'value' => 1.29],
            ['industry_id' => 14, 'dimension_id' => $dimensionMap['Vict'], 'value' => 1.4],
            ['industry_id' => 15, 'dimension_id' => $dimensionMap['Vict'], 'value' => 1.81],
            ['industry_id' => 16, 'dimension_id' => $dimensionMap['Vict'], 'value' => 1.4],
            
            // Entitlement benchmarks (16 industries)
            ['industry_id' => 1, 'dimension_id' => $dimensionMap['Ent'], 'value' => 1.43],
            ['industry_id' => 2, 'dimension_id' => $dimensionMap['Ent'], 'value' => 1.78],
            ['industry_id' => 3, 'dimension_id' => $dimensionMap['Ent'], 'value' => 1.53],
            ['industry_id' => 4, 'dimension_id' => $dimensionMap['Ent'], 'value' => 1.17],
            ['industry_id' => 5, 'dimension_id' => $dimensionMap['Ent'], 'value' => 1.39],
            ['industry_id' => 6, 'dimension_id' => $dimensionMap['Ent'], 'value' => 1.27],
            ['industry_id' => 7, 'dimension_id' => $dimensionMap['Ent'], 'value' => 1.4],
            ['industry_id' => 8, 'dimension_id' => $dimensionMap['Ent'], 'value' => 1.32],
            ['industry_id' => 9, 'dimension_id' => $dimensionMap['Ent'], 'value' => 1.26],
            ['industry_id' => 10, 'dimension_id' => $dimensionMap['Ent'], 'value' => 1.29],
            ['industry_id' => 11, 'dimension_id' => $dimensionMap['Ent'], 'value' => 1.78],
            ['industry_id' => 12, 'dimension_id' => $dimensionMap['Ent'], 'value' => 1.4],
            ['industry_id' => 13, 'dimension_id' => $dimensionMap['Ent'], 'value' => 1.49],
            ['industry_id' => 14, 'dimension_id' => $dimensionMap['Ent'], 'value' => 1.5],
            ['industry_id' => 15, 'dimension_id' => $dimensionMap['Ent'], 'value' => 1.46],
            ['industry_id' => 16, 'dimension_id' => $dimensionMap['Ent'], 'value' => 1.69]
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
        
        echo "Created " . count($benchmarks) . " industry benchmarks\n";
    }
    
    private function createBasicIndustries()
    {
        // Check if industries already exist
        $existingIndustries = DB::table('industries')->count();
        if ($existingIndustries > 0) {
            return; // Industries already exist
        }
        
        $industries = [
            ['name' => 'Hotel & Food Services'],
            ['name' => 'Adminstrative & Support Services'],
            ['name' => 'Agriculture, Forestry, Outdoors'],
            ['name' => 'Arts, Entertainment, and Recreation'],
            ['name' => 'Healthcare'],
            ['name' => 'Pharmaceuticals'],
            ['name' => 'Construction & Real Estate'],
            ['name' => 'Education'],
            ['name' => 'Energy, Oil, Gas, Mining'],
            ['name' => 'Government'],
            ['name' => 'Corporate/Executive Managers'],
            ['name' => 'Manufacturing'],
            ['name' => 'Professional Services'],
            ['name' => 'Retail/Wholesale'],
            ['name' => 'Utilities'],
            ['name' => 'Transportation & Warehouse']
        ];
        
        foreach ($industries as $industry) {
            DB::table('industries')->insert([
                'name' => $industry['name'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
    }
}
