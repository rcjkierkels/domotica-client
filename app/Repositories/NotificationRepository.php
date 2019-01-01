<?php

namespace App\Repositories;

use App\Models\Event;
use App\Models\Notification;
use stdClass;

class NotificationRepository
{

    public function getNotificationDataFromEvent(Event $event) : ?stdClass
    {
        $notification = Notification::where('client_id', $event->client_id)
            ->where('task_id', $event->task_id)
            ->where('state', $event->data->state)
            ->first();

        if (empty($notification)) {
            return null;
        }

        return $notification->data;
    }

}