<?php

namespace App\Notifications;

use App\Enums\ActionType;
use App\Models\Action;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
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
        if ($this->action->task()->events()->count()) {
            $state = $this->action->task()->events()->orderby('id', 'desc')->first()->data->state;
        }

        $data = [];
        $jobs = $this->action->task()->jobs()->get();

        foreach( $jobs  as $job )
        {
            $data[] = [
                'job_id' => $job->id,
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
