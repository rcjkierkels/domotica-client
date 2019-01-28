<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{

    protected $table = 'tasks';

    protected $guarded = [];

    public $timestamps = true;

    public function jobs()
    {
        return $this->belongsToMany(Job::class);
    }

    public function actions()
    {
        return $this->hasMany(Action::class);
    }

}
