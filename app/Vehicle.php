<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    //
    public function user()
    {
        return $this->belongsTo(User::class,'added_by'); //custom foreign key
    }
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('M d Y');
    }
}
