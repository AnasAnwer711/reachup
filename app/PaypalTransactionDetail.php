<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaypalTransactionDetail extends Model
{
    protected $guarded = [];

    public function advisor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
