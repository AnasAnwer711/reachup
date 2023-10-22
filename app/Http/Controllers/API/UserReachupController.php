<?php

namespace App\Http\Controllers\API;

use App\Coupon;
use App\CronNotification;
use App\DefaultSetting;
use App\UserReachup;
use App\Http\Resources\UserReachup as UserReachupResource;
use App\ReachupCoupon;
use App\User;
use App\UserNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserReachupController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // where('status', 'pending')->orW
            $pending = UserReachup::whereIn('status', ['process','reject'])->where(function ($q){
                $q->where('user_id',auth()->user()->id)->orWhere('advisor_id', auth()->user()->id);
            })->whereHas('payment', function($p){
                $p->whereNotNull('authorization_id');
            });
            $active = UserReachup::where('status', 'accept')->where(function ($q){
                $q->where('user_id',auth()->user()->id)->orWhere('advisor_id', auth()->user()->id);
            });
            $completed = UserReachup::whereIn('status', ['completed', 'cancel'])->where(function ($q){
                $q->where('user_id',auth()->user()->id)->orWhere('advisor_id', auth()->user()->id);
            });
            // $completed = UserReachup::where(function ($q){
            //     $q->where('status','completed')->orWhere('status', 'cancel');
            // })->where(function ($q){
            //     $q->where('user_id',auth()->user()->id)->orWhere('advisor_id', auth()->user()->id);
            // });
            
            // $data['new'] = $pending->get();
            $data['new'] = UserReachupResource::collection($pending->get());
            $data['active'] = UserReachupResource::collection($active->get());
            $data['completed'] = UserReachupResource::collection($completed->get());
            return $this->sendResponse($data, 'User reachup retrieved successfully.');
        } catch (\Throwable $th) {
            // dd($th);
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
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
        //
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
            

            $validator = Validator::make($input, [
                'advisor_id' => 'required|integer',
                'sub_category_id' => 'required|integer',
                'from_time' => 'required',
                'to_time' => 'required',
                'date' => 'required',
                'reachup_subject' => 'required',
                'charges' => 'required|numeric',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors()->first());       
            }

            $advisor_verified = User::checkAdvisorIsVerified($input['advisor_id']);
            if(!$advisor_verified['success']){
                return $this->sendError($advisor_verified['message']);       
            }
            
            $coupon = null;
            if(isset($input['coupon_id'])){
                $coupon = Coupon::find($input['coupon_id']);
                if($coupon){
                    if($coupon->advisor_id != $input['advisor_id']){
                        return $this->sendError('Coupon not assosiate with the current advisor');
                    }
                } else {
                    return $this->sendError('Coupon not exist');
                }
            }

            $input['user_id'] = auth()->user()->id;
            $input['status'] = 'pending';
            $input['paid_charges'] = $input['charges'];
            
            // dd($input);

            // $notification['title'] = 'User Request';
            // $notification['message'] = 'New request has been initiated from user '.auth()->user()->name;
            // $notification['type'] = 'request';
            // $notification['user_id'] = $input['advisor_id'];
            // $notification['created_by'] = $input['user_id'];

            // $datetime = $input['date'].' '.$input['from_time'];
            $reachup_sd = $input['date'].' '.$input['from_time'];
            $parse_date = Carbon::parse($reachup_sd, auth()->user()->timezone)->setTimezone('UTC');
            $datetime = $parse_date->toDateTimeString();
            //create cron notification for before 5 hour notification for pending or process state
            $cronNotification1['user_id'] = $input['user_id'];
            $cronNotification1['advisor_id'] = $input['advisor_id'];
            $cronNotification1['datetime'] = date('Y-m-d H:i:s', strtotime($datetime. ' -5 hours'));
            $cronNotification1['before'] = '5 hours';
            $cronNotification1['status'] = 'pending';

            //create cron notification for after 1 minute if pending or process state remains
            $cronNotification2['user_id'] = $input['user_id'];
            $cronNotification2['advisor_id'] = $input['advisor_id'];
            $cronNotification2['datetime'] = date('Y-m-d H:i:s', strtotime($datetime. ' +1 minute'));
            $cronNotification2['after'] = '1 minute';
            $cronNotification2['status'] = 'pending';


            // //create cron notification for before 24 hour notification 
            // $cronNotification1['user_id'] = $input['user_id'];
            // $cronNotification1['advisor_id'] = $input['advisor_id'];
            // $cronNotification1['datetime'] = date('Y-m-d H:i:s', strtotime($datetime. ' -1 day'));
            // $cronNotification1['before'] = '24 hours';
            // // dd($cronNotification1);
            
            // //create cron notification for before 10 min notification 
            // $cronNotification2['user_id'] = $input['user_id'];
            // $cronNotification2['advisor_id'] = $input['advisor_id'];
            // $cronNotification2['datetime'] =date('Y-m-d H:i:s', strtotime($datetime. ' -10 minutes'));
            // $cronNotification2['before'] = '10 mins';

            
            // dd($cronNotification1,$cronNotification2);
            unset($input['coupon_id']);
            
            // dd($reachup_payment,$reachup_payment['paypal_order_id']);
            DB::beginTransaction();
            $reachup['reachup'] = UserReachup::create($input);
            if($coupon){
                $reachup_coupon = [
                    'user_reachup_id' => $reachup['reachup']['id'],
                    'coupon_id' => $coupon->id,
                    'code' => $coupon->code,
                    'start' => $coupon->start,
                    'end' => $coupon->end,
                    'percentage' => $coupon->percentage,
                ];
                ReachupCoupon::create($reachup_coupon);
            }
            $reachup_payment = UserReachup::reachup_payment($reachup['reachup']['id']);
            $reachup['reachup'] = UserReachup::find($reachup['reachup']['id']);
            if(!$reachup_payment['success']){
                return $this->sendError($reachup_payment['message']);
            }
            $reachup['reachup']['paypal_order_id'] = $reachup_payment['paypal_order_id'];
            // $notify = UserNotification::notification([$input['advisor_id']], $notification);
            // if($notify){
                // $notification['user_reachup_id'] = $reachup['reachup']['id'];
                $cronNotification1['user_reachup_id'] = $reachup['reachup']['id'];
                $cronNotification2['user_reachup_id'] = $reachup['reachup']['id'];
                // $notification['notification'] = UserNotification::create($notification);
                CronNotification::create($cronNotification1);
                CronNotification::create($cronNotification2);
                DB::commit();
                return $this->sendResponse(UserReachupResource::collection($reachup), 'User reachup created successfully.');
            // } else {
            //     DB::rollBack();
            //     return $this->sendError('User notify unsuccessfuly');
            // }
        } catch (\Throwable $th) {
            DB::rollBack();
            // dd($th);
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }
        }
    }

    // public function reachup_update(Request $request)
    // {
    //     try {
    //         $input = $request->all();
    //         $validator = Validator::make($input, [
    //             'reachup_id' => 'required|integer',
    //             'status' => 'required',
    //         ]);
    
    //         if($validator->fails()){
    //             return $this->sendError('Validation Error.', $validator->errors()->first());       
    //         }

    //         $reachup = UserReachup::find($request->reachup_id);

    //         if(!$reachup){
    //             return $this->sendError('No reachup found.');
    //         }

    //         unset($input['reachup_id']);
    //         $reachup->fill($input)->save();
    //         if($input['status'] == '' || $input['status'] == null){
    //             $data['reachup'] = UserReachup::find($request->reachup_id);
    //             return $this->sendResponse(UserReachupResource::collection($data), 'User reachup status updated successfully.');
    //         }
    //         if($input['status'] == 'process'){
    //             $keyword = 'accept';
    //         } else if ($input['status'] == 'reject'){
    //             $keyword = 'reject';
    //         } else if ($input['status'] == 'completed'){
    //             $keyword = 'complet';
    //         }
    //         $notification['title'] = 'Request ' .$keyword.'ed';
    //         $notification['message'] = 'Your request has been '.$keyword.'ed from advisor '.auth()->user()->name;
    //         $notification['type'] = 'request';
    //         $notification['user_id'] = $reachup->user_id;
    //         $notification['created_by'] = auth()->user()->id;
    //         // UserReachup::where('id', $request->reachup_id)->update(['status'=>$request->status, 'duration'=>$request->duration, 'token_id'=>$request->token_id]);
    //         $data['reachup'] = UserReachup::find($request->reachup_id);
    //         $notify = UserNotification::notification([$notification['user_id']], $notification);
    //         if($notify){
    //             $notification['user_reachup_id'] = $request->reachup_id;
    //             $notification['notification'] = UserNotification::create($notification);
    //             return $this->sendResponse(UserReachupResource::collection($data), 'User reachup status updated successfully.');
    //         } else {
    //             if(\App::environment() == 'production'){
    //             return $this->sendError('Error Occured');
    //         } else {
    //             return $this->sendError('Error Occured', $th->getMessage());
    //         }
    // //         }

    // //     } catch (\Throwable $th) {
    // //         if(\App::environment() == 'production'){
    //             return $this->sendError('Error Occured');
    //         } else {
    //             return $this->sendError('Error Occured', $th->getMessage());
    //         }
    //     }
    // }

    /**
     * Display the specified resource.
     *
     * @param  \App\UserReachup  $userReachup
     * @return \Illuminate\Http\Response
     */
    public function show(UserReachup $userReachup)
    {
        try {
            $data['reachup'] = $userReachup;
            return $this->sendResponse(UserReachupResource::collection($data), 'User reachup retrieved successfully.');
        } catch (\Throwable $th) {
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UserReachup  $userReachup
     * @return \Illuminate\Http\Response
     */
    public function edit(UserReachup $userReachup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserReachup  $userReachup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserReachup $userReachup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserReachup  $userReachup
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserReachup $userReachup)
    {
        //
    }
}
