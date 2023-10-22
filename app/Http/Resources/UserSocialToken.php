<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSocialToken extends JsonResource
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
            // 'id' => $this->id,
            'social_token' => $this->social_token,
            'login_type' => $this->login_type,
            'social_url' => $this->social_url,
        ];
    }
}
