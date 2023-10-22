<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];
    protected $appends = ['have_subcategories', 'category_interests', 'is_selected', 'revenue_generated'];

    public function sub_categories()
    {
        return $this->hasMany(Self::class, 'parent_id');
    }

    public function user_interests()
    {
        if(auth()->user()){
            return $this->hasMany(UserInterest::class, 'sub_category_id')->where('user_id', auth()->user()->id);
        } else {
            return $this->hasMany(UserInterest::class, 'sub_category_id');
        }
    }

    public function user_cat_interests()
    {
        
        return $this->hasMany(UserInterest::class, 'sub_category_id');
        
    }

    public function user_interest_categories()
    {
        return $this->hasManyThrough(UserInterest::class, Self::class, 'parent_id', 'sub_category_id');
    }

    public function getHaveSubcategoriesAttribute()
    {
        // dd($this->sub_categories);
        if(count($this->sub_categories) > 0)
            return 1;
        else 
            return 0;
    }

    public function getIsSelectedAttribute()
    {
        if(count($this->user_interests) > 0 )
            return 1;
        else 
            return 0;
    }

    public function getCategoryInterestsAttribute()
    {
        if(count($this->sub_categories) > 0)
            return $this->user_interest_categories->count();
        else 
            return $this->user_cat_interests->count();
    }

    public function parent_category()
    {
        return $this->belongsTo(Self::class, 'parent_id');
    }

    public function user_reachups()
    {
        return $this->hasMany(UserReachup::class, 'sub_category_id');
    }

    public function user_reachups_categories()
    {
        return $this->hasManyThrough(UserReachup::class, Self::class, 'parent_id', 'sub_category_id');
    }

    public function getRevenueGeneratedAttribute()
    {
        if(count($this->sub_categories) > 0)
            return $this->user_reachups_categories->sum('charges');
        else 
            return $this->user_reachups->sum('charges');
    }

    public function category_keywords()
    {
        return $this->hasMany(CategoryKeyword::class);
    }


}
