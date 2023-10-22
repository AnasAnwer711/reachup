<?php

namespace App\Http\Resources;

use App\Http\Resources\AdvisorAvailability as AdvisorAvailabilityResource;
use App\Http\Resources\UserSocialToken as UserSocialTokenResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AdvisorDetailAvailability extends JsonResource
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
            'personal_info' => $this->personal_info,
            'charges' => $this->session_rate,
            'image' => $this->user->image,
            'featured_image' => $this->featured_image,
            'rating' => round($this->user->avg_rating, 2),
            'social_tokens' => UserSocialTokenResource::collection($this->user->social_tokens),
            'availabilities' => AdvisorAvailabilityResource::collection($this->availabilities),
        ];
    }
}
