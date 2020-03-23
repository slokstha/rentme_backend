<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $hidden = [ 'updated_at'];
    protected $guarded = ['id']; //fillable
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
