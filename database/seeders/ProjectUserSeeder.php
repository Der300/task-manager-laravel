<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projectData = DB::table('projects')->select('id', 'assigned_to', 'client_id')->get()->toArray(); //[['id' => 1,'assigned_to' => 12,'client_id' => 3,]]

        $taskData = DB::table('tasks')->select('id', 'assigned_to', 'project_id')->get()->toArray();; //[['id' => 1,'assigned_to' => 12,'project_id' => 3,]]

        foreach ($projectData as $project) {
            $userIds = [];

            if (!empty($project->assigned_to)) {
                $userIds[] = $project->assigned_to;
            }
            if (!empty($project->client_id)) {
                $userIds[] = $project->client_id;
            }

            foreach ($taskData as $task) {
                if ($task->project_id === $project->id && !empty($task->assigned_to)) {
                    $userIds[] = $task->assigned_to;
                }
            }

            $userIds = array_unique($userIds);

            foreach ($userIds as $userId) {
                DB::table('project_user')->updateOrInsert(
                    [
                        'project_id' => $project->id,
                        'user_id' => $userId,
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
