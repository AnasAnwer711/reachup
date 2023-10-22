<?php

namespace App\Http\Resources;

use App\UserFollow;
use Illuminate\Http\Resources\Json\JsonResource;

class FellowDetail extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // $is_follow = UserFollow::where('follower_id',$request->id)->where('following_id', auth()->user()->id)->first() ? '1' : '0';
        if($request->type == 'following'){
            
            $following_id = $this->follower_user->id ?? null;
            $following_name = $this->follower_user->name ?? null;
            $following_image = $this->follower_user->image ?? null;
        } else {
            $following_id = $this->following_user->id ?? null;
            $following_name = $this->following_user->name ?? null;
            $following_image = $this->following_user->image ?? null;
        }
        $is_follow = UserFollow::where('following_id',$following_id)->where('follower_id', auth()->user()->id)->first() ? '1' : '0';
        $auth_user = $following_id == auth()->user()->id ? '1' : '0';
        return [
            'user_id' => $following_id,
            'user_name' => $following_name,
            'user_image' => $following_image,
            'is_followed' => $is_follow,
            'auth_user' => $auth_user,
        ];
    }
}
