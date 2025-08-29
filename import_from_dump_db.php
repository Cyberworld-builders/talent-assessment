#!/usr/bin/env php
<?php

/**
 * Assessment Data Import Script - From Dump Database
 * =================================================
 * 
 * This script imports assessment data from the dump_data database into the Laravel system.
 * It queries the dump database directly and imports all related data.
 * 
 * Usage:
 *     php import_from_dump_db.php [--dry-run]
 * 
 * Options:
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

class DumpDatabaseImporter
{
    private $dryRun = false;
    private $dumpDb;

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
        
        // Create connection to dump database
        $this->dumpDb = new PDO(
            'mysql:host=mysql;dbname=dump_data;charset=utf8',
            'root',
            'root_password',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    public function import()
    {
        echo "Starting assessment import from dump_data database\n";
        
        if ($this->dryRun) {
            echo "DRY RUN MODE - No data will be actually imported\n";
        }
        
        echo "----------------------------------------\n";

        // Get all assessments from dump database
        $stmt = $this->dumpDb->query("SELECT * FROM assessments ORDER BY id");
        $assessments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "Found " . count($assessments) . " assessments to import\n\n";

        DB::beginTransaction();

        try {
            // Import supporting data first
            $this->importLanguages();
            $this->importIndustries();
            $this->importUsers();

            // Import assessments and their relations
            foreach ($assessments as $assessmentData) {
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

    private function importLanguages()
    {
        echo "Importing languages...\n";
        
        $stmt = $this->dumpDb->query("SELECT * FROM languages");
        $languages = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

    private function importIndustries()
    {
        echo "Importing industries...\n";
        
        $stmt = $this->dumpDb->query("SELECT * FROM industries");
        $industries = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

    private function importUsers()
    {
        echo "Importing users...\n";
        
        $stmt = $this->dumpDb->query("SELECT * FROM users WHERE id IN (SELECT DISTINCT user_id FROM assessments WHERE user_id IS NOT NULL)");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            'user_id' => $assessmentData['user_id'] ?? 1, // Default to admin user
            'name' => $assessmentData['name'],
            'description' => $assessmentData['description'],
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
            'custom_fields' => $assessmentData['custom_fields'],
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
        $this->importQuestions($assessmentData['id'], $assessment->id);
        $this->importDimensions($assessmentData['id'], $assessment->id);
        $this->importTranslations($assessmentData['id'], $assessment->id);
        $this->importWeights($assessmentData['id'], $assessment->id);

        echo "  Assessment imported successfully\n\n";
    }

    private function importQuestions($assessmentId)
    {
        $stmt = $this->dumpDb->prepare("SELECT * FROM questions WHERE assessment_id = ? ORDER BY id");
        $stmt->execute([$assessmentId]);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                'anchors' => $questionData['anchors'],
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

    private function importDimensions($assessmentId)
    {
        $stmt = $this->dumpDb->prepare("SELECT * FROM dimensions WHERE assessment_id = ? ORDER BY id");
        $stmt->execute([$assessmentId]);
        $dimensions = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            $this->importBenchmarks($dimension->id);
        }

        echo "    Created " . $this->stats['dimensions']['created'] . " dimensions\n";
    }

    private function importBenchmarks($dimensionId)
    {
        $stmt = $this->dumpDb->prepare("SELECT * FROM benchmarks WHERE dimension_id = ?");
        $stmt->execute([$dimensionId]);
        $benchmarks = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

    private function importTranslations($assessmentId)
    {
        $stmt = $this->dumpDb->prepare("SELECT * FROM translations WHERE assessment_id = ?");
        $stmt->execute([$assessmentId]);
        $translations = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            $this->importTranslatedQuestions($translation->id);
        }

        echo "    Created " . $this->stats['translations']['created'] . " translations\n";
    }

    private function importTranslatedQuestions($translationId)
    {
        $stmt = $this->dumpDb->prepare("SELECT * FROM translated_questions WHERE translation_id = ?");
        $stmt->execute([$translationId]);
        $translatedQuestions = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                'anchors' => $tqData['anchors'] ?? null,
                'created_at' => $tqData['created_at'],
                'updated_at' => $tqData['updated_at'],
            ];

            if (!$this->dryRun) {
                TranslatedQuestion::create($tqCreateData);
            }

            $this->stats['translated_questions']['created']++;
        }
    }

    private function importWeights($assessmentId)
    {
        $stmt = $this->dumpDb->prepare("SELECT * FROM weights WHERE assessment_id = ?");
        $stmt->execute([$assessmentId]);
        $weights = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                'weights' => $weightData['weights'],
                'divisions' => $weightData['divisions'],
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
$options = getopt('', ['dry-run', 'help']);

if (isset($options['help'])) {
    echo file_get_contents(__FILE__, false, null, 0, strpos(file_get_contents(__FILE__), '*/') + 2);
    exit(0);
}

$dryRun = isset($options['dry-run']);

try {
    $importer = new DumpDatabaseImporter($dryRun);
    $importer->import();
    
    echo "\nImport completed successfully!\n";
    
} catch (Exception $e) {
    echo "\nERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
