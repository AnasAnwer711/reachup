<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserRating as UserRatingResource;

class AdvisorDetail extends JsonResource
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
            'user_id' => $this->user_id,
            'name' => $this->user->name,
            'username' => $this->user->username,
            'is_online' => $this->user->is_online,
            'personal_info' => $this->personal_info,
            'charges' => $this->session_rate,
            'image' => $this->user->image,
            'featured_image' => $this->featured_image,
            'rating' => round($this->user->avg_rating,2),
            'ratings' => UserRatingResource::collection($this->user->ratings),
        ];
    }
}
