<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdvisorAvailability extends JsonResource
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
            'from_time' => $this->from_time,
            'to_time' => $this->to_time,
            'day' => [$this->day],
        ];
    }
}
