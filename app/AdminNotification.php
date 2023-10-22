<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $guarded = [];

    public function action_user()
    {
        return $this->belongsTo(User::class, 'action_by');
    }
    public function source_user()
    {
        return $this->belongsTo(User::class, 'source_id');
    }
    public function target_user()
    {
        return $this->belongsTo(User::class, 'target_id');
    }
}
