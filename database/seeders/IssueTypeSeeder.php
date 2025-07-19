<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IssueTypeSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        // Bug, Feature, Task, Improvement, Design, Testing, Support
        DB::table('issue_types')->insert([
            [
                'name' => 'Bug',
                'code' => 'bug',
                'description' => 'A problem or error in the system.',
                'created_at'=> $now,
                'updated_at'=> $now,
            ],
            [
                'name' => 'Feature',
                'code' => 'feature',
                'description' => 'A new feature request or enhancement.',
                'created_at'=> $now,
                'updated_at'=> $now,
            ],
            [
                'name' => 'Task',
                'code' => 'task',
                'description' => 'General work item not classified as bug or feature.',
                'created_at'=> $now,
                'updated_at'=> $now,
            ],
            [
                'name' => 'Improvement',
                'code' => 'improvement',
                'description' => 'Improvement to existing features or performance.',
                'created_at'=> $now,
                'updated_at'=> $now,
            ],
            [
                'name' => 'Design',
                'code' => 'design',
                'description' => 'Design or UI/UX related work.',
                'created_at'=> $now,
                'updated_at'=> $now,
            ],
            [
                'name' => 'Testing',
                'code' => 'testing',
                'description' => 'Testing and quality assurance activities.',
                'created_at'=> $now,
                'updated_at'=> $now,
            ],
            [
                'name' => 'Support',
                'code' => 'support',
                'description' => 'Technical support or troubleshooting.',
                'created_at'=> $now,
                'updated_at'=> $now,
            ],
        ]);
    }
}
