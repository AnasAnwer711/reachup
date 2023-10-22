<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaypalTransaction extends Model
{
    protected $guarded = [];

    public function reachup()
    {
        return $this->belongsTo(UserReachup::class);
    }
}
