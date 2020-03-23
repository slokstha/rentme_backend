<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    //
    public function user()
    {
        return $this->belongsTo(User::class,'added_by'); //custom foreign key
    }

}
