<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserRating as UserRatingResource;
use App\Http\Resources\AdvisorDetail as AdvisorDetailResource;

class AdvisorDetailWithPaginate extends JsonResource
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
            'data' => AdvisorDetailResource::collection($this->data),
            'current_page' => $this->current_page,
            'from' => $this->from,
            'last_page' => $this->last_page,
            'last_page_url' => $this->last_page_url,
            'next_page_url' => $this->next_page_url,
            'path' => $this->path,
            'per_page' => $this->per_page,
            'prev_page_url' => $this->prev_page_url,
            'to' => $this->to,
            'total' => $this->total,

        ];
    }
}
