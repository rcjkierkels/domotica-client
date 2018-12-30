<?php

namespace App\Notifications;

use App\Models\Event;
use App\Repositories\NotificationRepository;
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
        /** @var NotificationRepository $notificationRepository */
        $notificationRepository = app()->make(NotificationRepository::class);
        $notificationData = $notificationRepository->getNotificationDataFromEvent($this->event);

        return OneSignalMessage::create()
            ->subject($notificationData->title)
            ->body($notificationData->description)
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
