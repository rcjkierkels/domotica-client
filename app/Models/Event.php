<?php

namespace App\Models;

use App\Repositories\SubscriptionRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Event extends Model
{

    use Notifiable;

    protected $table = 'events';

    protected $guarded = [];

    public $timestamps = true;

    public function routeNotificationForOneSignal()
    {
        /** @var SubscriptionRepository $subscriptionRepository */
        $subscriptionRepository = app()->make(SubscriptionRepository::class);
        $subscribers = $subscriptionRepository->getSubscribers($this);

        return $subscribers->pluck('device_uuid');
    }

}
