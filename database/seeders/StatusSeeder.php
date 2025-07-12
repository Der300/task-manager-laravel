<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Open -> In Progress -> In Review -> Done, Cancel.
        DB::table('statuses')->insert([
            [
                'name' => 'Open',
                'code' => 'open',
                'description' => 'The task has been created and is awaiting processing.',
                'order' => 1,
                'color' => '#3498db', 
            ],
            [
                'name' => 'In Progress',
                'code' => 'in_progress',
                'description' => 'The task is currently being worked on.',
                'order' => 2,
                'color' => '#fec007', 
            ],
            [
                'name' => 'In Review',
                'code' => 'in_review',
                'description' => 'The task is being reviewed before completion.',
                'order' => 3,
                'color' => '#9b59b6', // purple
            ],
            [
                'name' => 'Done',
                'code' => 'done',
                'description' => 'The task has been completed successfully.',
                'order' => 4,
                'color' => '#27a844', // green
            ],
            [
                'name' => 'Cancel',
                'code' => 'cancel',
                'description' => 'The task has been cancelled and will not be completed.',
                'order' => 5,
                'color' => '#525a45', // brown
            ],
            [
                'name' => 'Pending',
                'code' => 'pending',
                'description' => 'The task has been delayed and will be completed later.',
                'order' => 5,
                'color' => '#525a45', // red
            ],
        ]);
    }
}
