<?php

namespace App\Notifications;

use App\Models\Comment;
use Auth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentAdded extends Notification
{
    use Queueable;
    protected $comment;
    protected $taskId;
    protected $createdByName;
    /**
     * Create a new notification instance.
     */
    public function __construct(Comment $comment, string $taskId, string $createdByName)
    {
        $this->comment = $comment;
        $this->taskId = $taskId;
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
    public function toDataBase(object $notifiable): array
    {
        return [
            'created_by' => $this->createdByName,
            'assigned_to' => $this->comment->user->name,
            'title' => 'You have been had a new comment in your task',
            'object_name' => $this->comment->body,
            'url' => route('tasks.show', ['task' => $this->taskId, 'comment_id' => $this->comment->id]),
            'type' => 'comment',
        ];
    }
}
