<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = ['added_by','title','price','owner_name','service_area','contact']; //fillable
    public function user()
    {
        return $this->belongsTo(User::class,'added_by'); //custom foreign key
    }
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('M d Y');
    }
}
