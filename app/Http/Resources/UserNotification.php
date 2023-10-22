<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserNotification extends JsonResource
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
            'user_reachup_id' => $this->user_reachup_id,
            'target_id' => $this->target_id,
            'created_by' => $this->created_by,
            'title' => $this->title,
            'message' => $this->message,
            'read' => $this->read,
            'delivered' => $this->delivered,
            'image' => $this->image,
            'type' => $this->type,
            'created_at' => $this->created_at,
        ];
    }
}
