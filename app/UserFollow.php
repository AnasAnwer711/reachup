<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserFollow extends Model
{
    protected $guarded = [];

    public function following()
    {
        return $this->belongsTo(User::class, 'following_id');
    }

    public function following_user()
    {
        return $this->belongsTo(User::class, 'following_id');
    }

    public function follower_user()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }
    
}
