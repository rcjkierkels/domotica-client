<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $table = 'attachments';

    protected $guarded = [];

    public $timestamps = true;

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

}
