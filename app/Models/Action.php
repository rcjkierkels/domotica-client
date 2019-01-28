<?php

namespace App\Models;

use App\Enums\ActionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Action extends Model
{
    use Notifiable;

    /**
     * These action types require some sort of notification
     */
    const NotifiableTypes = [
        ActionType::PUSH,
        ActionType::MAIL
    ];

    protected $table = 'actions';

    protected $guarded = [];

    public $timestamps = true;

    public function getEvaluationAttribute($data)
    {
        return json_decode($data);
    }

    public function getDataAttribute($data)
    {
        return json_decode($data);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function subscribers()
    {
        $this->belongsToMany(User::class);
    }

    public function evaluate(Event $event) : bool
    {
        switch($this->evaluation->type)
        {
            case 'equation':
                return $event->data->{$this->evaluation->data->variable} === $this->evaluation->data->value;
                break;

            default:
                throw new \Exception("Cannot evaluate action because action type {$this->evaluation->type} is unknown");
        }
    }

    public function routeNotificationForOneSignal()
    {
        return $this->subscribers()->get()->pluck('device_uuid');
    }

}
