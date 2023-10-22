<?php

namespace App\Http\Resources;

use DateTime;
use DateTimeZone;
use Illuminate\Http\Resources\Json\JsonResource;

class ReachupPayment extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $created_at_dateTime = new DateTime($this->created_at);
        $created_at_dateTime->setTimeZone(new DateTimeZone(auth()->user()->timezone));
        $created_at_timezone = $created_at_dateTime->format('Y-m-d H:i:s'); 
        
        $updated_at_dateTime = new DateTime($this->updated_at);
        $updated_at_dateTime->setTimeZone(new DateTimeZone(auth()->user()->timezone));
        $updated_at_timezone = $updated_at_dateTime->format('Y-m-d H:i:s'); 
        
        return [
            'id' => $this->id,
            'card_type' => $this->card_type,
            'card_image' => $this->card_image,
            'card_no' => $this->card_no,
            'charges' => $this->payType == 'spent' ? ($this->state == 'cancel' ? $this->user_fee : $this->amount) : $this->advisor_fee,
            'created_at' => $created_at_timezone,
            'updated_at' => $updated_at_timezone,
            'status' => $this->state == 'cancel' ? 'Refunded' : ($this->state == 'void' ? ($this->action_by == 1 ? 'Cancelled' : 'Rejected'): 'None'),
        ];
    }
}
