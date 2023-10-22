<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserKeyword extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // dd($request);
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'keyword' => $this->keyword,
        ];
    }
}
