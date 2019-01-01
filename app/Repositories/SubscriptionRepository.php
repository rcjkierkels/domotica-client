<?php

namespace App\Repositories;

use App\Models\Event;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Collection;

class SubscriptionRepository
{
    public function getSubscribers(Event $event) : Collection
    {
        return Subscription::where('client_id', $event->client_id)
            ->where('task_id', $event->task_id)
            ->get();
    }

}