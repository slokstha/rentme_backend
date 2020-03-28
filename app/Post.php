<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $hidden = [ 'updated_at']; //hidden
    protected $guarded = ['id']; //fillable
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('M d Y');
    }
}
