<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InitiateUser extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // if ($this->id == 2) {
        //     dd($this);
        // }
        return [
            'id' => $this->id,
            'advisor_detail_id' => $this->advisor->id ?? null,
            'username' => $this->username,
            'email' => $this->email,
            'name' => $this->name,
            'phone' => $this->phone,
            'image' => $this->image,
            'rating' => round($this->avg_rating,2),
            'no_of_followers' => $this->my_total_followers,
            'no_of_followings' => $this->my_total_followings,
        ];
    }
}
