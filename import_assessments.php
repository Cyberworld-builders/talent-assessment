#!/usr/bin/env php
<?php

/**
 * Assessment Data Import Script
 * =============================
 * 
 * This script imports assessment data from a JSON file into the Laravel system.
 * It handles all relationships and ensures data integrity.
 * 
 * Usage:
 *     php import_assessments.php [--file assessments.json] [--dry-run]
 * 
 * Options:
 *     --file      JSON file to import (default: assessments_from_dump.json)
 *     --dry-run   Show what would be imported without actually importing
 *     --help      Show this help message
 */

// Bootstrap Laravel
require_once __DIR__ . '/bootstrap/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Assessment;
use App\Question;
use App\Dimension;
use App\Translation;
use App\TranslatedQuestion;
use App\Weight;
use App\User;
use App\Language;
use App\Industry;
use App\Benchmark;
use Illuminate\Support\Facades\DB;

class AssessmentImporter
{
    private $dryRun = false;
    private $stats = [
        'assessments' => ['created' => 0, 'updated' => 0, 'skipped' => 0],
        'questions' => ['created' => 0, 'updated' => 0, 'skipped' => 0],
        'dimensions' => ['created' => 0, 'updated' => 0, 'skipped' => 0],
        'benchmarks' => ['created' => 0, 'updated' => 0, 'skipped' => 0],
        'translations' => ['created' => 0, 'updated' => 0, 'skipped' => 0],
        'translated_questions' => ['created' => 0, 'updated' => 0, 'skipped' => 0],
        'weights' => ['created' => 0, 'updated' => 0, 'skipped' => 0],
        'users' => ['created' => 0, 'updated' => 0, 'skipped' => 0],
        'languages' => ['created' => 0, 'updated' => 0, 'skipped' => 0],
        'industries' => ['created' => 0, 'updated' => 0, 'skipped' => 0],
    ];

    public function __construct($dryRun = false)
    {
        $this->dryRun = $dryRun;
    }

    public function import($jsonFile)
    {
        echo "Starting assessment import from: {$jsonFile}\n";
        
        if ($this->dryRun) {
            echo "DRY RUN MODE - No data will be actually imported\n";
        }
        
        echo "----------------------------------------\n";

        // Read and parse JSON file
        if (!file_exists($jsonFile)) {
            throw new Exception("JSON file not found: {$jsonFile}");
        }

        $jsonData = file_get_contents($jsonFile);
        $data = json_decode($jsonData, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON: " . json_last_error_msg());
        }

        if (!isset($data['assessments'])) {
            throw new Exception("Invalid JSON structure: missing 'assessments' key");
        }

        echo "Found " . count($data['assessments']) . " assessments to import\n\n";

        DB::beginTransaction();

        try {
            // Import supporting data first
            $this->importLanguages($data['assessments']);
            $this->importIndustries($data['assessments']);
            $this->importUsers($data['assessments']);

            // Import assessments and their relations
            foreach ($data['assessments'] as $assessmentData) {
                $this->importAssessment($assessmentData);
            }

            if ($this->dryRun) {
                DB::rollback();
                echo "\nDRY RUN COMPLETED - No changes were made to the database\n";
            } else {
                DB::commit();
                echo "\nIMPORT COMPLETED SUCCESSFULLY\n";
            }

        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }

        $this->printStats();
    }

    private function importLanguages($assessments)
    {
        echo "Importing languages...\n";
        
        $languages = [];
        foreach ($assessments as $assessment) {
            foreach ($assessment['translations'] as $translation) {
                if (isset($translation['language_id'], $translation['language_name'], $translation['language_code'])) {
                    $languages[$translation['language_id']] = [
                        'id' => $translation['language_id'],
                        'name' => $translation['language_name'],
                        'code' => $translation['language_code'],
                        'native_name' => $translation['language_name'], // Default to same as name
                        'terms' => '{}' // Empty JSON object
                    ];
                }
            }
        }

        foreach ($languages as $langData) {
            $existing = Language::find($langData['id']);
            
            if ($existing) {
                $this->stats['languages']['skipped']++;
                continue;
            }

            if (!$this->dryRun) {
                Language::create($langData);
            }
            
            $this->stats['languages']['created']++;
            echo "  Created language: {$langData['name']}\n";
        }
    }

    private function importIndustries($assessments)
    {
        echo "Importing industries...\n";
        
        $industries = [];
        foreach ($assessments as $assessment) {
            foreach ($assessment['dimensions'] as $dimension) {
                foreach ($dimension['benchmarks'] as $benchmark) {
                    if (isset($benchmark['industry_id'], $benchmark['industry_name'])) {
                        $industries[$benchmark['industry_id']] = [
                            'id' => $benchmark['industry_id'],
                            'name' => $benchmark['industry_name'],
                            'created_at' => $benchmark['created_at'] ?? now(),
                            'updated_at' => $benchmark['updated_at'] ?? now(),
                        ];
                    }
                }
            }
        }

        foreach ($industries as $industryData) {
            $existing = Industry::find($industryData['id']);
            
            if ($existing) {
                $this->stats['industries']['skipped']++;
                continue;
            }

            if (!$this->dryRun) {
                Industry::create($industryData);
            }
            
            $this->stats['industries']['created']++;
            echo "  Created industry: {$industryData['name']}\n";
        }
    }

    private function importUsers($assessments)
    {
        echo "Importing users...\n";
        
        $users = [];
        foreach ($assessments as $assessment) {
            if (isset($assessment['user']) && $assessment['user']) {
                $userData = $assessment['user'];
                $users[$userData['id']] = $userData;
            }
        }

        foreach ($users as $userData) {
            $existing = User::find($userData['id']);
            
            if ($existing) {
                $this->stats['users']['skipped']++;
                continue;
            }

            // Prepare user data for creation
            $userCreateData = [
                'id' => $userData['id'],
                'name' => $userData['name'] ?? 'Unknown User',
                'username' => $userData['username'] ?? 'user_' . $userData['id'],
                'email' => $userData['email'] ?? null,
                'password' => bcrypt('password'), // Default password
                'client_id' => $userData['client_id'] ?? null,
                'language_id' => $userData['language_id'] ?? null,
                'completed_profile' => $userData['completed_profile'] ?? false,
                'completed_research' => $userData['completed_research'] ?? false,
                'job_title' => $userData['job_title'] ?? null,
                'job_family' => $userData['job_family'] ?? null,
                'industry_id' => $userData['industry_id'] ?? null,
                'timezone' => $userData['timezone'] ?? null,
                'registered' => $userData['registered'] ?? false,
                'verified_email' => $userData['verified_email'] ?? true,
                'picture' => $userData['picture'] ?? null,
                'created_at' => $userData['created_at'] ?? now(),
                'updated_at' => $userData['updated_at'] ?? now(),
            ];

            if (!$this->dryRun) {
                User::create($userCreateData);
            }
            
            $this->stats['users']['created']++;
            echo "  Created user: {$userCreateData['name']} ({$userCreateData['username']})\n";
        }
    }

    private function importAssessment($assessmentData)
    {
        echo "Importing assessment: {$assessmentData['name']}\n";

        // Check if assessment already exists
        $existing = Assessment::find($assessmentData['id']);
        
        if ($existing) {
            echo "  Assessment already exists, skipping\n";
            $this->stats['assessments']['skipped']++;
            return;
        }

        // Prepare assessment data
        $assessmentCreateData = [
            'id' => $assessmentData['id'],
            'user_id' => isset($assessmentData['user']) && $assessmentData['user'] ? $assessmentData['user']['id'] : 1, // Default to admin user
            'name' => $assessmentData['name'],
            'description' => $assessmentData['description'],
            'instructions' => $assessmentData['instructions'] ?? null,
            'logo' => $assessmentData['logo'],
            'background' => $assessmentData['background'],
            'paginate' => $assessmentData['paginate'],
            'items_per_page' => $assessmentData['items_per_page'],
            'translation' => $assessmentData['translation'],
            'language' => $assessmentData['language'],
            'whitelabel' => $assessmentData['whitelabel'],
            'company_labeled_for' => $assessmentData['company_labeled_for'],
            'timed' => $assessmentData['timed'],
            'time_limit' => $assessmentData['time_limit'],
            'use_custom_fields' => $assessmentData['use_custom_fields'],
            'custom_fields' => $this->serializeCustomFields($assessmentData['custom_fields']),
            'target' => $assessmentData['target'],
            'created_at' => $assessmentData['created_at'],
            'updated_at' => $assessmentData['updated_at'],
            'last_modified' => $assessmentData['last_modified'],
        ];

        if (!$this->dryRun) {
            $assessment = Assessment::create($assessmentCreateData);
        } else {
            $assessment = new Assessment($assessmentCreateData);
            $assessment->id = $assessmentData['id'];
        }

        $this->stats['assessments']['created']++;

        // Import related data
        $this->importQuestions($assessmentData['questions'], $assessment->id);
        $this->importDimensions($assessmentData['dimensions'], $assessment->id);
        $this->importTranslations($assessmentData['translations'], $assessment->id);
        $this->importWeights($assessmentData['weights'], $assessment->id);

        echo "  Assessment imported successfully\n\n";
    }

    private function importQuestions($questions, $assessmentId)
    {
        echo "  Importing " . count($questions) . " questions...\n";

        foreach ($questions as $questionData) {
            $existing = Question::find($questionData['id']);
            
            if ($existing) {
                $this->stats['questions']['skipped']++;
                continue;
            }

            $questionCreateData = [
                'id' => $questionData['id'],
                'content' => $questionData['content'],
                'assessment_id' => $assessmentId,
                'number' => $questionData['number'],
                'type' => $questionData['type'],
                'dimension_id' => $questionData['dimension_id'],
                'anchors' => $this->serializeAnchors($questionData['anchors']),
                'practice' => $questionData['practice'] ?? false,
                'created_at' => $questionData['created_at'],
                'updated_at' => $questionData['updated_at'],
            ];

            if (!$this->dryRun) {
                Question::create($questionCreateData);
            }

            $this->stats['questions']['created']++;
        }

        echo "    Created " . $this->stats['questions']['created'] . " questions\n";
    }

    private function importDimensions($dimensions, $assessmentId)
    {
        echo "  Importing " . count($dimensions) . " dimensions...\n";

        foreach ($dimensions as $dimensionData) {
            $existing = Dimension::find($dimensionData['id']);
            
            if ($existing) {
                $this->stats['dimensions']['skipped']++;
                continue;
            }

            $dimensionCreateData = [
                'id' => $dimensionData['id'],
                'name' => $dimensionData['name'],
                'parent' => $dimensionData['parent'],
                'code' => $dimensionData['code'],
                'assessment_id' => $assessmentId,
                'definition' => $dimensionData['definition'] ?? null,
                'created_at' => $dimensionData['created_at'],
                'updated_at' => $dimensionData['updated_at'],
            ];

            if (!$this->dryRun) {
                $dimension = Dimension::create($dimensionCreateData);
            } else {
                $dimension = new Dimension($dimensionCreateData);
                $dimension->id = $dimensionData['id'];
            }

            $this->stats['dimensions']['created']++;

            // Import benchmarks for this dimension
            $this->importBenchmarks($dimensionData['benchmarks'], $dimension->id);
        }

        echo "    Created " . $this->stats['dimensions']['created'] . " dimensions\n";
    }

    private function importBenchmarks($benchmarks, $dimensionId)
    {
        foreach ($benchmarks as $benchmarkData) {
            $existing = Benchmark::find($benchmarkData['id']);
            
            if ($existing) {
                $this->stats['benchmarks']['skipped']++;
                continue;
            }

            $benchmarkCreateData = [
                'id' => $benchmarkData['id'],
                'dimension_id' => $dimensionId,
                'industry_id' => $benchmarkData['industry_id'],
                'value' => $benchmarkData['value'],
                'created_at' => $benchmarkData['created_at'],
                'updated_at' => $benchmarkData['updated_at'],
            ];

            if (!$this->dryRun) {
                Benchmark::create($benchmarkCreateData);
            }

            $this->stats['benchmarks']['created']++;
        }
    }

    private function importTranslations($translations, $assessmentId)
    {
        if (empty($translations)) {
            return;
        }

        echo "  Importing " . count($translations) . " translations...\n";

        foreach ($translations as $translationData) {
            $existing = Translation::find($translationData['id']);
            
            if ($existing) {
                $this->stats['translations']['skipped']++;
                continue;
            }

            $translationCreateData = [
                'id' => $translationData['id'],
                'user_id' => $translationData['user_id'],
                'assessment_id' => $assessmentId,
                'language_id' => $translationData['language_id'],
                'name' => $translationData['name'],
                'description' => $translationData['description'],
                'instructions' => $translationData['instructions'] ?? null,
                'created_at' => $translationData['created_at'],
                'updated_at' => $translationData['updated_at'],
            ];

            if (!$this->dryRun) {
                $translation = Translation::create($translationCreateData);
            } else {
                $translation = new Translation($translationCreateData);
                $translation->id = $translationData['id'];
            }

            $this->stats['translations']['created']++;

            // Import translated questions
            $this->importTranslatedQuestions($translationData['translated_questions'], $translation->id);
        }

        echo "    Created " . $this->stats['translations']['created'] . " translations\n";
    }

    private function importTranslatedQuestions($translatedQuestions, $translationId)
    {
        foreach ($translatedQuestions as $tqData) {
            $existing = TranslatedQuestion::find($tqData['id']);
            
            if ($existing) {
                $this->stats['translated_questions']['skipped']++;
                continue;
            }

            $tqCreateData = [
                'id' => $tqData['id'],
                'translation_id' => $translationId,
                'question_id' => $tqData['question_id'],
                'content' => $tqData['content'],
                'anchors' => $this->serializeAnchors($tqData['anchors'] ?? null),
                'created_at' => $tqData['created_at'],
                'updated_at' => $tqData['updated_at'],
            ];

            if (!$this->dryRun) {
                TranslatedQuestion::create($tqCreateData);
            }

            $this->stats['translated_questions']['created']++;
        }
    }

    private function importWeights($weights, $assessmentId)
    {
        if (empty($weights)) {
            return;
        }

        echo "  Importing " . count($weights) . " weights...\n";

        foreach ($weights as $weightData) {
            $existing = Weight::find($weightData['id']);
            
            if ($existing) {
                $this->stats['weights']['skipped']++;
                continue;
            }

            $weightCreateData = [
                'id' => $weightData['id'],
                'survey_id' => $weightData['survey_id'] ?? null,
                'assessment_id' => $assessmentId,
                'weights' => $this->serializeWeights($weightData['weights']),
                'divisions' => $this->serializeWeights($weightData['divisions']),
                'created_at' => $weightData['created_at'],
                'updated_at' => $weightData['updated_at'],
            ];

            if (!$this->dryRun) {
                Weight::create($weightCreateData);
            }

            $this->stats['weights']['created']++;
        }

        echo "    Created " . $this->stats['weights']['created'] . " weights\n";
    }

    private function serializeCustomFields($customFields)
    {
        if (is_null($customFields) || $customFields === '') {
            return null;
        }

        if (is_array($customFields)) {
            return serialize($customFields);
        }

        // If it's already a string, assume it's serialized
        return $customFields;
    }

    private function serializeAnchors($anchors)
    {
        if (is_null($anchors) || $anchors === '') {
            return '';
        }

        if (is_array($anchors)) {
            return serialize($anchors);
        }

        // If it's already a string, assume it's serialized
        return $anchors;
    }

    private function serializeWeights($weights)
    {
        if (is_null($weights) || $weights === '') {
            return '';
        }

        if (is_array($weights)) {
            return serialize($weights);
        }

        // If it's already a string, assume it's serialized
        return $weights;
    }

    private function printStats()
    {
        echo "\n========================================\n";
        echo "IMPORT STATISTICS\n";
        echo "========================================\n";

        foreach ($this->stats as $type => $counts) {
            $total = $counts['created'] + $counts['updated'] + $counts['skipped'];
            if ($total > 0) {
                echo sprintf("%-20s: %3d created, %3d updated, %3d skipped (total: %3d)\n", 
                    ucfirst($type), $counts['created'], $counts['updated'], $counts['skipped'], $total);
            }
        }

        echo "========================================\n";
    }
}

// Parse command line arguments
$options = getopt('', ['file:', 'dry-run', 'help']);

if (isset($options['help'])) {
    echo file_get_contents(__FILE__, false, null, 0, strpos(file_get_contents(__FILE__), '*/') + 2);
    exit(0);
}

$jsonFile = $options['file'] ?? 'assessments_from_dump.json';
$dryRun = isset($options['dry-run']);

try {
    $importer = new AssessmentImporter($dryRun);
    $importer->import($jsonFile);
    
    echo "\nImport completed successfully!\n";
    
} catch (Exception $e) {
    echo "\nERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
