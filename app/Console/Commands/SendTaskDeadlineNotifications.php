<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Todo;
use App\Notifications\TaskDeadlineNotification;
use Carbon\Carbon;

class SendTaskDeadlineNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-task-deadline-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //ユーザー情報と締切が明日のタスクを取得
        $todos = Todo::with('user')
            ->whereDate('due_date', Carbon::tomorrow())
            ->get();

        foreach ($todos as $todo) {
            $todo->user->notify(new TaskDeadlineNotification($todo));
        }
    }
}
