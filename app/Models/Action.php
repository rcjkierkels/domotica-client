<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
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

}
