<?php

namespace App\Notifications;

use App\Enums\ActionType;
use App\Models\Action;
use App\Models\Event;
use App\Models\Log;
use App\Repositories\ActionRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class EventCreated extends Notification
{
    use Queueable;

    /** @var Event */
    private $event;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
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
        // todo: different approach. This implementation only supports push actions, not actions in general

        /** @var ActionRepository $actionRepository */
        $actionRepository = app()->make(ActionRepository::class);
        $evaluatedPushActions = $actionRepository->getSpecificActionsForEvent($this->event, ActionType::PUSH);

        if ($evaluatedPushActions === null) {
            Log::info('EventCreated', 'Notification', 'Notification not send because no notification set');
            return;
        }

        // todo: support multiple push messages for single event
        if ($evaluatedPushActions->count() > 1) {
            Log::alert('EventCreated', 'Notification', 'Multiple push messages detected and ignored. Only one push message send');
        }

        /** @var Action $action */
        $action = $evaluatedPushActions->first();

        return OneSignalMessage::create()
            ->subject($action->data->title)
            ->body($action->data->description)
            ->setData('event', $this->event->toJson());
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
