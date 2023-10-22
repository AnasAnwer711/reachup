<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class UserReachup extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function advisor()
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'sub_category_id');
    }

    public function payment()
    {
        return $this->hasOne(PaypalTransaction::class, 'reachup_id');
    }

    public static function create_transaction_detail($reachup_id,$paypal_transaction_id, $action, $status, $payments)
    {
        try {
            TransactionDetail::updateOrCreate([
                'reachup_id' => $reachup_id,
                'action' => $action,
            ],[
                'paypal_transaction_id' => $paypal_transaction_id,
                'status' => $status,
                'user_fee' => $payments['user_fee'],
                'user_percentage' => $payments['user_percentage'],
                'advisor_fee' => $payments['advisor_fee'],
                'advisor_percentage' => $payments['advisor_percentage'],
                'reachup_fee' => $payments['reachup_fee'],
                'reachup_percentage' => $payments['reachup_percentage'],
            ]);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public static function reachup_payment($reachup_id)
    {
        try {
            //code...
        
            // $input = $request->all();
            // dd($input);
            // $validator = Validator::make($input, [
            //     'card_no' => 'required',
            //     'title' => 'required',
            //     'expiry' => 'required',
            //     'cvc' => 'required',
            //     'card_type' => 'required',
            //     'reachup_id' => 'required|integer',
            // ]);
    
            // if($validator->fails()){
            //     return $this->sendError('Validation Error.', $validator->errors()->first());       
            // }

            if(PaypalTransaction::where('reachup_id', $reachup_id)->exists()){
                // return $this->sendError('Reachup payment already exist');    
                $error = [
                    'success' => false,
                    'message' => 'Reachup payment already exist',
                ]   ;
                return $error;
                
            }
            // dd('out');

            $couponExist = false;
            $reachup_coupon = ReachupCoupon::where('user_reachup_id', $reachup_id)->first();
            if($reachup_coupon){
                $couponExist = true;
            }
            
            // dd('**** **** **** '.(preg_replace( "#(.*?)(\d{4})$#", "$2", $request->card_no)));
            // dd($input);
            //paypal transaction get payment id
            $card_image = url('/').'/cards/paypal.png';
            $default_settings = DefaultSetting::first();

            $reachup = UserReachup::findOrFail($reachup_id);
            $charges = $reachup->charges;
            $org_charges = $reachup->charges;
            $paid_charges = $reachup->paid_charges;
            $default_rules = DefaultRule::where('rule_type', 'default')->get();

            $user_percentage = 0;
            $advisor_percentage = $default_rules->where('concern', 'advisor')->first()->percentage ?? null;
            $reachup_percentage = $default_rules->where('concern', 'platform')->first()->percentage ?? null;
            $user_fee = ($charges*$user_percentage)/100;
            $advisor_fee = ($charges*$advisor_percentage)/100;
            $reachup_fee = ($charges*$reachup_percentage)/100;

            $org_advisor_fee = $advisor_fee;
            $org_advisor_percentage = $advisor_percentage;

            if($couponExist){
                $advisor_percentage = intVal($advisor_percentage)-intVal($reachup_coupon->percentage);
                $advisor_fee = ($advisor_fee*$reachup_coupon->percentage)/100;
                $charges = $charges-(($charges*$reachup_coupon->percentage)/100);
                $reachup->update(['charges'=>$charges]);
            }

            //add aditional charges in reachup if setup
            $default_setting = DefaultSetting::first();
            // dd($default_setting);
            if($default_setting->is_additional_charges){
                $handling_percentage = $default_setting->percentage ?? 0;
                $handling_charges = round(($charges*$default_setting->percentage)/100,2);
                $handling_title = $default_setting->title ?? null;
                $capped_amount = env('MIN_CAPPED_AMOUNT');
                if(isset($capped_amount) && $handling_charges < $capped_amount){
                    $handling_charges = $capped_amount;
                }
                $paid_charges = round(floatVal($charges) + floatVal($handling_charges),2);
                UserReachup::where('id', $reachup_id)->update([
                    'handling_percentage'=> $handling_percentage,
                    'handling_charges'=> $handling_charges,
                    'paid_charges'=> $paid_charges,
                    'handling_title'=> $handling_title,
                ]);
            }

            $ch = curl_init();
            $client_id = Crypt::decryptString($default_settings['client_id']);

            $secret_id = Crypt::decryptString($default_settings['secret_id']);
            
            curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/oauth2/token");
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch, CURLOPT_USERPWD, $client_id.":".$secret_id);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
            
            $result = curl_exec($ch);
            // dd($result);
            if(empty($result)) {
                $error = [
                    'success' => false,
                    'message' => 'Paypal Error: No response',
                ]   ;
                return $error;
            }
            else
            {
                $json = json_decode($result);
                // dd($json);
                $token = $json->access_token;
            }
            
            curl_close($ch);
            // dd($token);
            //replace email address with advisor email address
            // "payee": {
            //     "email_address": "sb-an433c1305718@business.example.com"
            // }
            $advisor = User::find($reachup->advisor_id);

            $data = '{
                "intent":"AUTHORIZE",
                "purchase_units":[{
                    "amount": {
                        "currency_code": "SGD",
                        "value": "'.$paid_charges.'"
                    }
                }]
            }';
            if(!$advisor->paypal_email)
                $payStatus = 'unpaid';
            else 
                $payStatus = 'paid';
            
            // dd(json_decode($data));
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v2/checkout/orders/");
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER,array(
                                "Content-Type:application/json",
                                "Authorization:Bearer ".$token )
                    );
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            // dd($ch);
            
            $result = curl_exec($ch);
            // dd($result);
            if(empty($result)) {
                $error = [
                    'success' => false,
                    'message' => 'Paypal Error: No response',
                ]   ;
                return $error;
            }
            else
            {
                $paypal_order = json_decode($result);
            }
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // dd($paypal_order->id);
            $paypal_order_id = $paypal_order->id;
            $links = $paypal_order->links;
            $key = array_search('approve', array_column($links, 'rel'));

            $link = $links[$key]->href;
            // dd($link);
            curl_close($ch);
            // dd($httpcode, $paypal_order);
            if($httpcode == 201){

                try {
                    // dd($paypal_order, auth()->user());
                    DB::beginTransaction();
                    $paypal = PaypalTransaction::create([
                        'payment_id'   => $paypal_order->id, //replace with payment id from paypal
                        'reachup_id' => $reachup_id,
                        'card_no' => null,
                        'card_type' => null,
                        'card_image' => $card_image,
                        'currency_code' => 'SGD',
                        'org_amount' => $org_charges,
                        'amount' => $charges,
                        'charged_amount' => $paid_charges,
                        'user_fee' => $user_fee,
                        'user_percentage' => $user_percentage,
                        'org_advisor_fee' => $org_advisor_fee,
                        'advisor_fee' => $advisor_fee,
                        'org_advisor_percentage' => $org_advisor_percentage,
                        'advisor_percentage' => $advisor_percentage,
                        'reachup_fee' => $reachup_fee,
                        'reachup_percentage' => $reachup_percentage,
                        'state' => 'intent',
                        'action_by' => auth()->user()->user_type_id,
                    ]);
                    // $detail = PaypalTransactionDetail::create([
                    //     'paypal_transaction_id'   => $paypal->id, //replace with payment id from paypal
                    //     'type' => 'advisor',
                    //     'user_id' => $advisor->id,
                    //     'status' => $payStatus,
                    //     'pay_amount' => $advisor_fee,
                    // ]);
                    $payments = [
                        'user_fee' => $user_fee,
                        'user_percentage' => $user_percentage,
                        'advisor_fee' => $advisor_fee,
                        'advisor_percentage' => $advisor_percentage,
                        'reachup_fee' => $reachup_fee,
                        'reachup_percentage' => $reachup_percentage,
                    ];
                    $success = UserReachup::create_transaction_detail($reachup_id, $paypal->id, 'intent', 'success', $payments);

                    // $notification['title'] = 'Reachup Payment Intent';
                    // $notification['message'] = 'Reachup payment has been intented';
                    // $notification['type'] = 'payment';
                    // $notification['user_id'] = $reachup->advisor_id;
                    // $notification['created_by'] = auth()->user()->id;

                    // $notify = UserNotification::notification([$reachup->advisor_id], $notification);
                    // if($notify){
                    //     $notification['user_reachup_id'] = $reachup->id;
                    //     $notification['notification'] = UserNotification::create($notification);
                    // }
                    if(!$success){
                        DB::rollBack();
                        $error = [
                            'success' => false,
                            'message' => 'Transaction not created successfully'
                        ];
                        return $error;
                        // $this->sendError('Transaction not created successfully');
                    } else {
                        DB::commit();
                    }
                    $response = [
                        'success' => true,
                        'message' => 'Payment intent authorize successfully.',
                        'paypal_order_id' =>$paypal_order_id
                    ];
                    return $response;
                    // return $this->sendResponse(['link'=>$link], 'Payment intent authorize successfully.');
                } catch (\Throwable $th) {
                    DB::rollBack();
                    // dd($th);
                    if(\App::environment() == 'production'){
                $error = [
                    'success' => false,
                    'message' => 'Error Occured'
                ];
                        return $error;
                // $this->sendError('Error Occured');
            } else {
                $error = [
                    'success' => false,
                    'message' => $th->getMessage()
                ];
                return $error;
                // $this->sendError('Error Occured', $th->getMessage());
            }
                }
                
            } else {
                $error = [
                    'success' => false,
                    'message' => 'Http code not matched'
                ];
                return $error;
                // $this->sendError('Http code not matched');

            }
        } catch (\Throwable $th) {
            // dd($th);
            if(\App::environment() == 'production'){
                $error = [
                    'success' => false,
                    'message' => 'Http code not matched'
                ];
            } else {
                $error = [
                    'success' => false,
                    'message' => $th->getMessage(),
                ];
            }
            return $error;
            
        }
    }
}
