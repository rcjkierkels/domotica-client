<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';

    protected $guarded = [];

    public $timestamps = true;

    public function getDataAttribute($data)
    {
        return json_decode($data);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

}
