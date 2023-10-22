<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdvisorDetail extends Model
{
    protected $guarded = [];

    protected $appends = ['is_following'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getIsFollowingAttribute()
    {
        if(auth()->user()){
            if(UserFollow::where('follower_id', auth()->user()->id)->where('following_id', $this->user_id)->exists()){
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    // public function availabilities()
    // {
    //     return $this->hasMany(AdvisorAvailability::class,'advisor_id')->groupBy('from_time', 'to_time');
    // }

    public function availabilities()
    {
        return $this->hasMany(ViewAdvisorAvailability::class,'advisor_id');
    }
}
