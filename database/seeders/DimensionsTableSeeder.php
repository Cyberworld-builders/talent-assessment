<?php

use Illuminate\Database\Seeder;
use App\Assessment;
use App\Dimension;

class DimensionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing dimensions
        Dimension::truncate();

        // Get all assessments
        $assessments = Assessment::all();

        foreach ($assessments as $assessment) {
            $this->seedDimensionsForAssessment($assessment);
        }
    }

    /**
     * Seed dimensions for a specific assessment based on its type/name
     */
    private function seedDimensionsForAssessment($assessment)
    {
        $dimensions = [];

        // Determine dimensions based on assessment name (case-insensitive)
        $assessmentName = strtolower(trim($assessment->name));
        switch ($assessmentName) {
            case 'personality assessment':
                $dimensions = [
                    ['name' => 'Extraversion'],
                    ['name' => 'Agreeableness'],
                    ['name' => 'Conscientiousness'],
                    ['name' => 'Neuroticism'],
                    ['name' => 'Openness'],
                    ['name' => 'Emotional Intelligence'],
                    ['name' => 'Adaptability'],
                    ['name' => 'Team Collaboration'],
                    ['name' => 'Communication Style'],
                    ['name' => 'Leadership Potential']
                ];
                break;

            case 'cognitive ability test':
                $dimensions = [
                    ['name' => 'Problem Solving', 'description' => 'Analytical thinking and logical reasoning'],
                    ['name' => 'Critical Thinking', 'description' => 'Evaluation and analysis of information'],
                    ['name' => 'Numerical Reasoning', 'description' => 'Mathematical and statistical analysis'],
                    ['name' => 'Verbal Reasoning', 'description' => 'Language comprehension and verbal logic'],
                    ['name' => 'Spatial Reasoning', 'description' => 'Visual and spatial problem solving'],
                    ['name' => 'Memory', 'description' => 'Information retention and recall ability'],
                    ['name' => 'Processing Speed', 'description' => 'Quick thinking and rapid information processing'],
                    ['name' => 'Attention to Detail', 'description' => 'Accuracy and thoroughness in tasks'],
                    ['name' => 'Pattern Recognition', 'description' => 'Identifying trends and patterns in data'],
                    ['name' => 'Decision Making', 'description' => 'Logical and systematic decision processes']
                ];
                break;

            case 'leadership potential':
                $dimensions = [
                    ['name' => 'Vision and Strategy', 'description' => 'Long-term thinking and strategic planning'],
                    ['name' => 'Communication', 'description' => 'Effective verbal and written communication'],
                    ['name' => 'Team Building', 'description' => 'Creating and maintaining high-performing teams'],
                    ['name' => 'Decision Making', 'description' => 'Sound judgment and timely decisions'],
                    ['name' => 'Emotional Intelligence', 'description' => 'Self-awareness and relationship management'],
                    ['name' => 'Influence and Persuasion', 'description' => 'Ability to motivate and inspire others'],
                    ['name' => 'Conflict Resolution', 'description' => 'Managing disagreements and finding solutions'],
                    ['name' => 'Adaptability', 'description' => 'Flexibility in changing circumstances'],
                    ['name' => 'Innovation', 'description' => 'Creative thinking and process improvement'],
                    ['name' => 'Integrity', 'description' => 'Ethical behavior and trustworthiness']
                ];
                break;

            default:
                // Generic dimensions for any other assessment types
                $dimensions = [
                    ['name' => 'Problem Solving', 'description' => 'Analytical thinking and solution development'],
                    ['name' => 'Communication', 'description' => 'Effective verbal and written communication'],
                    ['name' => 'Teamwork', 'description' => 'Collaboration and team effectiveness'],
                    ['name' => 'Leadership', 'description' => 'Leadership qualities and influence'],
                    ['name' => 'Adaptability', 'description' => 'Flexibility and change management'],
                    ['name' => 'Innovation', 'description' => 'Creative thinking and process improvement'],
                    ['name' => 'Decision Making', 'description' => 'Sound judgment and timely decisions'],
                    ['name' => 'Time Management', 'description' => 'Organization and prioritization skills'],
                    ['name' => 'Attention to Detail', 'description' => 'Accuracy and thoroughness'],
                    ['name' => 'Customer Focus', 'description' => 'Understanding and meeting customer needs']
                ];
                break;
        }

        // Create dimensions for this assessment
        foreach ($dimensions as $dimensionData) {
            Dimension::create([
                'name' => $dimensionData['name'],
                'description' => $dimensionData['description'],
                'assessment_id' => $assessment->id,
                'parent' => 0, // Top-level dimensions
                'code' => strtoupper(substr(str_replace(' ', '_', $dimensionData['name']), 0, 8)), // Generate a code
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ]);
        }

        $this->command->info("Created " . count($dimensions) . " dimensions for assessment: " . $assessment->name);
    }
}
