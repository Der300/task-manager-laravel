<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskUpdated extends Notification
{
    use Queueable;
    protected $task;
    protected $createdByName;
    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task, string $createdByName)
    {
        $this->task = $task;
        $this->createdByName = $createdByName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; //nơi lưu
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'created_by' => $this->createdByName,
            'assigned_to' => $this->task->assignedUser->name,
            'title' => 'Your task has been updated',
            'object_name' => $this->task->name,
            'url' => route('tasks.show', $this->task->id),
            'type' => 'task',
        ];
    }
}
