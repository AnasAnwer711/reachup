<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserInterest extends Model
{
    protected $guarded = [];

    public function sub_category()
    {
        return $this->belongsTo(Category::class, 'sub_category_id');
    }

    public function advisors()
    {
        return $this->belongsTo(User::class, 'user_id')->where('user_type_id', 2);
    }
}
