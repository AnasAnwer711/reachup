<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $guarded = [];
    public function parent_reporting()
    {
        return $this->belongsTo(Self::class, 'parent_id');
    }

    public function sub_reportings()
    {
        return $this->hasMany(Self::class, 'parent_id');
    }
}
