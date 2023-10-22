<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryKeyword extends JsonResource
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
            'keyword' => $this->keyword,
            'category' => $this->category->title ?? '',
        ];
    }
}
