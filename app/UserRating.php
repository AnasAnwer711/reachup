<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserRating extends Model
{
    protected $guarded = [];

    public function source()
    {
        return $this->belongsTo(User::class, 'source_id');
    }

    public function getCreatedAtAttribute($value)
    {
        $timezone = 'Asia/Singapore';
        $user = User::find($this->source_id);
        if($user){
            if(isset($user->timezone))
                $timezone = $user->timezone;
        } 
        $formatted_date = date('Y-m-d H:i:s',strtotime($value));
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $formatted_date);
        $date->setTimezone($timezone);
        return $date->toDateTimeString();
    }
}
