<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserReport extends JsonResource
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
            'report_name' => $this->report->title,
            'source_id' => $this->source_id,
            'source_name' => $this->source->name,
            'created_at' => $this->created_at,
        ];
    }
}
