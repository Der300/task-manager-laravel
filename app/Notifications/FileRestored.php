<?php

namespace App\Notifications;

use App\Models\File;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FileRestored extends Notification
{
    use Queueable;
    protected $file;
    protected $assignedTask;
    protected $createdByName;
    /**
     * Create a new notification instance.
     */
    public function __construct(File $file, string $assignedTask, string $createdByName)
    {
        $this->file = $file;
        $this->assignedTask = $assignedTask;
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
            'assigned_to' => $this->assignedTask,
            'title' => 'Your file has been restored!',
            'object_name' => $this->file->original_name,
            'url' => route('myfiles.index', ['file_id' => $this->file->id]),
            'type' => 'file',
        ];
    }
}
