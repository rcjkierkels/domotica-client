<?php

namespace App\Repositories;

use App\Models\Event;
use App\Models\Notification;

class NotificationRepository
{

    public function getNotificationDataFromEvent(Event $event) : ?object
    {
        $notification = Notification::where('client_id', $event->client_id)
            ->where('event', $event->event)
            ->where('state', $event->data->state)
            ->first();

        if (empty($notification)) {
            return null;
        }

        return $notification->data;
    }

}