<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class TaskDeadlineNotification extends Notification
{
    public $task;

    public function __construct($task)
    {
        $this->task = $task;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => '「' . $this->task->title . '」の締切が近づいています。',
            'task_id' => $this->task->id,
            'due_date' => $this->task->due_date,
        ];
    }
}
