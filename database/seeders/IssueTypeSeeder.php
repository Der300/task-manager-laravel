<?php

namespace Database\Seeders;

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
        // Bug, Feature, Task, Improvement, Design, Testing, Support
        DB::table('issue_types')->insert([
            [
                'name' => 'Bug',
                'code' => 'bug',
                'description' => 'A problem or error in the system.',
            ],
            [
                'name' => 'Feature',
                'code' => 'feature',
                'description' => 'A new feature request or enhancement.'
            ],
            [
                'name' => 'Task',
                'code' => 'task',
                'description' => 'General work item not classified as bug or feature.'
            ],
            [
                'name' => 'Improvement',
                'code' => 'improvement',
                'description' => 'Improvement to existing features or performance.'
            ],
            [
                'name' => 'Design',
                'code' => 'design',
                'description' => 'Design or UI/UX related work.'
            ],
            [
                'name' => 'Testing',
                'code' => 'testing',
                'description' => 'Testing and quality assurance activities.'
            ],
            [
                'name' => 'Support',
                'code' => 'support',
                'description' => 'Technical support or troubleshooting.',
            ],
        ]);
    }
}
