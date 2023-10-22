<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserRating extends JsonResource
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
            'rate' => round($this->rate, 2),
            'reviews' => $this->reviews,
            'source_id' => $this->source_id,
            'source_name' => $this->source->name,
            'created_at' => $this->created_at,
        ];
    }
}
