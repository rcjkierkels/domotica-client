<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{

    protected $table = 'client_events';

    protected $guarded = [];

    public $timestamps = true;

}
