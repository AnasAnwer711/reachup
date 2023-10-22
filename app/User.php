<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'avg_rating', 'my_total_followers', 'my_total_followings', 'my_total_ratings', 'my_total_reachups', 'total_earning'
    ];
    
    public function user_type()
    {
        return $this->belongsTo(UserType::class);
    }
    
    public function keywords()
    {
        return $this->hasMany(UserKeyword::class);
    }

    public function interests()
    {
        return $this->hasMany(UserInterest::class);
    }
    
    public function blocks()
    {
        return $this->hasMany(UserBlock::class);
    }
    
    public function notifications()
    {
        return $this->hasMany(UserNotification::class);
    }

    public function ratings()
    {
        return $this->hasMany(UserRating::class, 'target_id');
    }

    public function reports()
    {
        return $this->hasMany(UserReport::class, 'source_id');
    }
    
    public function follows()
    {
        return $this->hasMany(UserFollow::class, 'following_id');
    }
    
    public function followers()
    {
        return $this->hasMany(UserFollow::class, 'follower_id');
    }
    
    public function except_auth_follows()
    {
        return $this->hasMany(UserFollow::class, 'following_id')->where('follower_id', '!=', auth()->user()->id);
    }
    
    public function except_auth_followers()
    {
        return $this->hasMany(UserFollow::class, 'follower_id')->where('following_id', '!=', auth()->user()->id);
    }

    public function social_tokens()
    {
        return $this->hasMany(UserSocialToken::class);
    }

    public function adv_reachups()
    {
        return $this->hasMany(UserReachup::class,'advisor_id');
    }
    public function user_reachups()
    {
        return $this->hasMany(UserReachup::class,'user_id');
    }
    
    public function advisor()
    {
        return $this->hasOne(AdvisorDetail::class);
    }

    public function getTotalEarningAttribute()
    {
        return $this->adv_reachups->sum('charges') ?? 0;
    }
    
    public function getTotalSpentAttribute()
    {
        return $this->user_reachups->sum('charges') ?? 0;
    }

    public function getAvgRatingAttribute()
    {
        return round($this->ratings->avg('rate'), 2) ?? 0;
    }
    
    public function getMyTotalFollowersAttribute()
    {
        if(auth()->user() && $this->id){
            // if(auth()->user()->is_superadmin){

            //     return DB::table('vw_user_fellowship')->where('user_id', $this->id)->first()->Followers;
            // } else {

            $exist = DB::table('vw_user_fellowship')->where('user_id', $this->id)->first();
            if($exist){
                return $exist->Followers;
            } else {
                return 0;
            }
            // }
        } else {
            return 0;
        }

    }
    
    public function getMyTotalFollowingsAttribute()
    {
        // dd($this);
        if(auth()->user() && $this->id){
            // if(auth()->user()->is_superadmin){

            //     return DB::table('vw_user_fellowship')->where('user_id', $this->id)->first()->Followings;
            // } else {
            $exist = DB::table('vw_user_fellowship')->where('user_id', $this->id)->first();
            if($exist){
                return $exist->Followings;
            } else {
                return 0;
            }
            // }
        } else {
            return 0;
        }
    }

    public function getMyTotalRatingsAttribute()
    {
        // dd(count(auth()->user()->user_reachups));
        if(auth()->user()){
            return count(auth()->user()->ratings);
        } else {
            return 0;
        }
    }

    public function getMyTotalReachupsAttribute()
    {
        if(auth()->user()){
            if(auth()->user()->user_type_id == 1)
                return count(auth()->user()->user_reachups);
            else 
                return count(auth()->user()->adv_reachups);
        } else {
            return 0;
        }
    }

    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }

    public function loginSecurity()
    {
        return $this->hasOne(LoginSecurity::class);
    }

    public function getCreatedAtAttribute($value)
    {
        $timezone = 'Asia/Singapore';
        if(isset($this->timezone)){
            $timezone = $this->timezone;
        }
        $formatted_date = date('Y-m-d H:i:s',strtotime($value));
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $formatted_date);
        $date->setTimezone($timezone);
        return $date->toDateTimeString();
    }


    public static function checkAdvisorIsVerified($user_id)
    {
        if(!$user_id){
            $error = [
                'success' => false,
                'message' => 'First argument is missing for advisor verification',
            ];
            return $error;
        }
        $findUser = User::find($user_id);
        if(!$findUser){
            $error = [
                'success' => false,
                'message' => 'No advisor exists',
            ];
            return $error;
        } else {

            $findAsAdvisor = $findUser->user_type_id;
            if($findAsAdvisor != 2){

                $error = [
                    'success' => false,
                    'message' => 'Wrong selection! Advisor must be an advisor',
                ];
                return $error;
            } else {
                if(!$findUser->email_verified_at){
                    $error = [
                        'success' => false,
                        'message' => 'Advisor email is not verified',
                    ];
                    return $error;
                } else {

                    if(!$findUser->advisor){
                        $error = [
                            'success' => false,
                            'message' => 'Advisor details are incomplete must be an advisor',
                        ];
                        return $error;
                    } else {
                        $error = [
                            'success' => true,
                            'message' => 'Advisor found with verification and validation',
                        ];
                        return $error;
                    }
                }
                
            }
        }
    }

}
