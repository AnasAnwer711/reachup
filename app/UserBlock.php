<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserBlock extends Model
{
    protected $guarded = [];

    public function blocked()
    {
        return $this->belongsTo(User::class, 'blocked_id');
    }
}
