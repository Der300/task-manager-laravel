<?php

namespace Database\Seeders;

use Carbon\Carbon;
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
                'color' => '#006fe5',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'In Progress',
                'code' => 'in_progress',
                'description' => 'The task is currently being worked on.',
                'order' => 2,
                'color' => '#fec007',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'In Review',
                'code' => 'in_review',
                'description' => 'The task is being reviewed before completion.',
                'order' => 3,
                'color' => '#c13df5ff', // purple
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Done',
                'code' => 'done',
                'description' => 'The task has been completed successfully.',
                'order' => 4,
                'color' => '#27a844', // green
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Cancel',
                'code' => 'cancel',
                'description' => 'The task has been cancelled and will not be completed.',
                'order' => 5,
                'color' => '#dc3546', // red
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Pending',
                'code' => 'pending',
                'description' => 'The task has been delayed and will be completed later.',
                'order' => 5,
                'color' => '#525a45', // brown
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ]);
    }
}
