<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\InitiateUser as InitiateUserResource;

class UserReachup extends JsonResource
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
            'auth_id' => auth()->user() ? auth()->user()->id : $this->user_id,
            'user_id' => $this->user_id,
            'initiate_user' => new InitiateUserResource($this->user),
            'reach_user' => new InitiateUserResource($this->advisor),
            'advisor_id' => $this->advisor_id,
            'from_time' => $this->from_time,
            'to_time' => $this->to_time,
            'date' => $this->date,
            'reachup_subject' => $this->reachup_subject,
            'status' => $this->status,
            'charges' => $this->charges,
            'duration' => $this->duration,
            'is_open' => $this->is_open,
            'is_muted' => $this->is_muted,
            'paypal_order_id' => $this->paypal_order_id,
        ];
    }
}
