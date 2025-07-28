<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentForceDeleted extends Notification
{
    use Queueable;
    protected $comment;
    protected $createdByName;

    /**
     * Create a new notification instance.
     */
    public function __construct(Comment $comment, string $createdByName)
    {
        $this->comment = $comment;
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
            'title' => 'Your comment was permanently deleted',
            'object_name' => $this->comment->body,
            'url' => route('notifications.index'),
            'type' => 'comment',
        ];
    }
}
