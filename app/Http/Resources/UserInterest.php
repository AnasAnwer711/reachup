<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserInterest extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // dd($this->sub_category->title);
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_type_id' => $this->user_type_id,
            'sub_category_id' => $this->sub_category_id,
            'sub_category_name' => $this->sub_category->title,
        ];
    }
}
