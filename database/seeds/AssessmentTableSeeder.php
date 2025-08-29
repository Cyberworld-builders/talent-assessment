<?php

use Illuminate\Database\Seeder;
use App\Assessment;

class AssessmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $assessments = [
            [
                'id' => 1,
                'user_id' => 1,
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
                'last_modified' => '0000-00-00 00:00:00',
            ],
            [
                'id' => 3,
                'user_id' => 1,
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
                'last_modified' => '0000-00-00 00:00:00',
            ],
            [
                'id' => 4,
                'user_id' => 1,
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
                'last_modified' => '0000-00-00 00:00:00',
            ],
            [
                'id' => 5,
                'user_id' => 1,
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
                'last_modified' => '0000-00-00 00:00:00',
            ],
            [
                'id' => 6,
                'user_id' => 1,
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
                'last_modified' => '2020-06-26 20:59:36',
            ],
            [
                'id' => 7,
                'user_id' => 1,
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
                'last_modified' => '2025-07-31 19:19:23',
            ],
        ];

        foreach ($assessments as $assessment) {
            Assessment::create($assessment);
        }

        echo "Created " . count($assessments) . " assessments:\n";
        foreach ($assessments as $assessment) {
            echo "- {$assessment['name']} (ID: {$assessment['id']})\n";
        }
    }
}
