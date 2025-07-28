<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectSoftDeleted extends Notification
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
            'created_by' => $this->createdByName,
            'assigned_to' => $this->project->assignedUser->name,
            'title' => 'Your project has been moved to recycle',
            'object_name' => $this->project->name,
            'url' => route('notifications.index'),
            'type' => 'project',
        ];
    }
}
