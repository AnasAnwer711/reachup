<?php

namespace App\Http\Resources;

use App\Http\Resources\AdvisorAvailability as AdvisorAvailabilityResource;
use App\Http\Resources\UserRating as UserRatingResource;
use App\Http\Resources\UserInterest as UserInterestResource;
use App\Http\Resources\UserSocialToken as UserSocialTokenResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AdvisorProfileDetail extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->user->name,
            'username' => $this->user->username,
            'personal_info' => $this->personal_info,
            'charges' => $this->session_rate,
            'image' => $this->user->image,
            'featured_image' => $this->featured_image,
            'rating' => round($this->user->avg_rating,2),
            'is_blocked' => $this->user->status == 'blocked' ? 1 : 0,
            'is_following' => $this->is_following,
            'no_of_followers' => $this->user->my_total_followers,
            'no_of_followings' => $this->user->my_total_followings,
            'availabilities' => AdvisorAvailabilityResource::collection($this->availabilities),
            'ratings' => UserRatingResource::collection($this->user->ratings),
            'interests' => UserInterestResource::collection($this->user->interests),
            'social_tokens' => UserSocialTokenResource::collection($this->user->social_tokens),
        ];
    }
}
