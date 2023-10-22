<?php

namespace App\Http\Resources;

use App\AdvisorDetail;
use App\DefaultRule;
use Illuminate\Http\Resources\Json\JsonResource;

class Coupon extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $platform_default_rules = DefaultRule::where('rule_type', 'default')->where('concern', 'platform')->first();
        $advisor_detail = AdvisorDetail::where('user_id', auth()->user()->id)->first();
        // $advisor_default_rules = DefaultRule::where('rule_type', 'default')->where('concern', 'advisor')->first(); 
        return [
            'id' => $this->id ?? '',
            'code' => $this->code ?? '',
            'start' => $this->start ?? '',
            'end' => $this->end ?? '',
            // 'reachup_charges' => $this['reachup_charges'] ?? 0,
            'advisor_charges' => $advisor_detail->session_rate ?? 0,
            // 'advisor_charges_after_discount' => $this['advisor_charges_after_discount'] ?? 0,
            'reachup_percentage' => $platform_default_rules->percentage ?? '',
            // 'advisor_percentage' => $advisor_default_rules->percentage ?? '',
            'is_active' => $this->is_active ?? '',
        ];
    }
}
