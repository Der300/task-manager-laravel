<?php

namespace App\Notifications;

use App\Models\Project;
use Auth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectAssigned extends Notification implements ShouldQueue
{
    use Queueable;
    protected $project;
    protected $createdByName;
    /**
     * Create a new notification instance.
     */
    public function __construct(Project $project, string $createdByName)
    {
        $this->project = $project;
        $this->createdByName = $createdByName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'created_by'=> $this->createdByName,
            'assigned_to' => $this->project->assignedUser->name,
            'title' => 'A new project has been assigned to you',
            'object_name' => $this->project->name,
            'url' => route('projects.show', $this->project),
            'type' => 'project',
        ];
    }

}
