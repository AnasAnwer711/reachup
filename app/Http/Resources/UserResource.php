<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserKeyword as UserKeywordResource;
use App\Http\Resources\UserRating as UserRatingResource;
use App\Http\Resources\UserSocialToken as UserSocialTokenResource;
use App\Http\Resources\UserFollow as UserFollowResource;
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // dd($this);
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'paypal_email' => $this->paypal_email,
            'name' => $this->name,
            'phone' => $this->phone,
            'bio' => $this->bio,
            'description' => $this->description,
            'image' => $this->image,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'address' => $this->address,
            'status' => $this->status,
            'rating' => $this->avg_rating,
            'no_of_followers' => $this->my_total_followers,
            'no_of_followings' => $this->my_total_followings,
            'no_of_ratings' => $this->my_total_ratings,
            'no_of_reachups' => $this->my_total_reachups,
            'user_type' => $this->user_type->name ?? null,
            // 'login_type' => $this->login_type,
            'profile_complete' => $this->profile_complete,
            'is_advisor_complete' => $this->advisor ? 1 : 0,
            'is_payment_detail_completed' => $this->is_payment_detail_completed,
            'social_tokens' => UserSocialTokenResource::collection($this->social_tokens),
            'keywords' => UserKeywordResource::collection($this->keywords),
            'ratings' => UserRatingResource::collection($this->ratings),
            // 'follows' => UserFollowResource::collection($this->follows),
        ];
    }
}
