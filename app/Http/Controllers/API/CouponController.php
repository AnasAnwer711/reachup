<?php

namespace App\Http\Controllers\API;

use App\AdvisorDetail;
use App\Coupon;
use App\DefaultRule;
use Illuminate\Http\Request;
use App\Http\Resources\Coupon as CouponResource;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class CouponController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $coupon['coupons'] = Coupon::where('is_active',1)->get();
            // dd($coupon);
            return $this->sendResponse(CouponResource::collection($coupon['coupons']), 'Coupon retrieved successfully.');
        } catch (\Throwable $th) {
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured: '.$th->getMessage());
            }  
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('coupon.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $input = $request->all();
            // dd($input);
            $validator = Validator::make($input, [
                'code' => 'required',
                'is_active' => 'required|boolean',
                'percentage' => 'required|integer|min:1|max:100',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors()->first());       
            }

            
            $user = auth()->user();
            if($user->user_type_id != 2){
                return $this->sendError('User must be an advisor');
            }
            

            $codeExist = Coupon::where('advisor_id', $user->id)->where('code', $input['code'])->where('is_active', 1)->first();
            if($codeExist){
                return $this->sendError('Code has already been taken');       
            }
            // $input['is_active'] = 1;
            $input['advisor_id'] = $user->id;
    
            $coupon['coupon'] = Coupon::create($input);

            return $this->sendResponse(CouponResource::collection($coupon), 'Coupon created successfully.');
        } catch (\Throwable $th) {
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured: '.$th->getMessage());
            }  
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function show(Coupon $coupon)
    {
        try {
            $data['coupon'] = $coupon;
            return $this->sendResponse(CouponResource::collection($data), 'Coupon retrieved successfully.');
        } catch (\Throwable $th) {
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured: '.$th->getMessage());
            }  
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function edit(Coupon $coupon)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Coupon $coupon)
    {
        try {
            $validator = Validator::make($request->all(), [ 
                'is_active' => 'required|in:1,0',
            ]);
                
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors()->first());       
            }

            if($coupon){
                $data['coupon'] = $coupon;
                $coupon->update(['is_active'=>$request->is_active]);
                return $this->sendResponse(CouponResource::collection($data), 'Coupon updated successfully.');
            } else {
                return $this->sendError('No coupon found');       
            }
        } catch (\Throwable $th) {
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured: '.$th->getMessage());
            }  
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function destroy(Coupon $coupon)
    {
        try {
            if($coupon){
                $data['coupon'] = $coupon;
                $coupon->delete();
                return $this->sendResponse([], 'Coupon deleted successfully.');
            } else {
                return $this->sendError('No coupon found');       
            }
        } catch (\Throwable $th) {
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured: '.$th->getMessage());
            }  
        }
    }

    public function apply_coupon(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'code' => 'required',
                'advisor_id' => 'required|integer',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors()->first());       
            }

            $user = User::find($input['advisor_id']);
            if(!$user)
                return $this->sendError('No user exist of given advisor');

            if($user->user_type_id != 2){
                return $this->sendError('Advisor selection is wrong');
            }

            

            if(!$user->advisor){
                return $this->sendError('Advisor details are incomplete');

            }

            $apply_coupon = Coupon::where('advisor_id', $input['advisor_id'])->where('code', $input['code'])->where('is_active', 1)->first();
            $data = [];
            // dd($apply_coupon);
            if($apply_coupon){
                //If End Date and start date both is given
                if($apply_coupon->start && $apply_coupon->end){

                    $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $apply_coupon->start);
                    $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $apply_coupon->end);
             
                    $check = Carbon::now()->between($startDate, $endDate);
                    if(!$check){
                        return $this->sendError('Coupon expired or Not applicable');
                    }
                }

                //If End Date is null and start date is given
                if($apply_coupon->start && !$apply_coupon->end){
                    if (Carbon::parse($apply_coupon->end)->gt(Carbon::now())){
                        return $this->sendError('Coupon not applicable');
                    }
                }
                //If Start Date is null and end date is given
                if(!$apply_coupon->start && $apply_coupon->end){
                    if (Carbon::parse($apply_coupon->end)->lt(Carbon::now())){
                        return $this->sendError('Coupon expired');
                    }
                }
                $rate = $user->advisor->session_rate;

                $data['coupon'] = [
                    'coupon_id'=> $apply_coupon->id,
                    'session_charges' => $rate,
                    'discount_percentage' => $apply_coupon->percentage,
                    'discount' => ($rate*$apply_coupon->percentage)/100,
                    'net_charges' => $rate-(($rate*$apply_coupon->percentage)/100),
                ];
                return $this->sendResponse($data, 'Coupon applied successfully.');
            } else {
                return $this->sendResponse($data, 'Coupon not found');

            }
        } catch (\Throwable $th) {
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured: '.$th->getMessage());
            }  
        }
    }

    public function coupon_initialize()
    {
        try {
            $user = auth()->user();
            if($user->user_type_id != 2){
                return $this->sendError('User must be an advisor');
            }

            $platform_default_rules = DefaultRule::where('rule_type', 'default')->where('concern', 'platform')->first();
            $advisor_detail = AdvisorDetail::where('user_id', $user->id)->first();

            $data['advisor_charges'] = $advisor_detail->session_rate;
            $data['reachup_percentage'] = $platform_default_rules->percentage;
            return $this->sendResponse($data, 'Coupon initialize charges get successfully.');
            
        } catch (\Throwable $th) {
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured: '.$th->getMessage());
            }  
        }
    }
}
