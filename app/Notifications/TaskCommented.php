<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskCommented extends Notification
{
    use Queueable;
    protected $comment;
    protected $taskId;
    /**
     * Create a new notification instance.
     */
    public function __construct(Comment $comment, string $taskId)
    {
        $this->comment = $comment;
        $this->taskId = $taskId;
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
    public function toDataBase(object $notifiable): array
    {
        return [
            'title' => 'You have been had a new comment in your task',
            'comment_id' => $this->comment->id,
            'comment_name' => $this->comment->name,
            'url' => route('tasks.show', $this->taskId),
            'type' => 'comment',
        ];
    }
}
