<?php

namespace App\Notifications;

use App\Enums\ActionType;
use App\Models\Action;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class ActionExecution extends Notification
{
    use Queueable;

    /** @var Action */
    private $action;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Action $action)
    {
        $this->action = $action;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [OneSignalChannel::class];
    }

    public function toOneSignal($notifiable)
    {
        if ($this->action->type !== ActionType::PUSH) {
            return;
        }

        return OneSignalMessage::create()
            ->subject($this->action->data->title)
            ->body($this->action->data->description)
            ->setData('jobs', json_encode($this->collectPushData()));
    }

    protected function collectPushData()
    {
        $state = -1;

        $lastEvent = Event::where('task_id', $this->action->task_id)->orderby('id', 'desc')->first();

        if (!empty($lastEvent)) {
            $state = $lastEvent->data->state;
        }

        $data = [];

        $jobs = DB::table('job_task')->where('task_id', $this->action->task_id)->get();

        foreach( $jobs  as $job )
        {
            $data[] = [
                'job_id' => $job->job_id,
                'state' => $state
            ];
        }

        return $data;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
