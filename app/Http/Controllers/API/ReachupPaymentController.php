<?php

namespace App\Http\Controllers\API;

use App\Coupon;
use App\CronNotification;
use App\DefaultRule;
use App\DefaultSetting;
use App\EmailTemplate;
use App\PaymentDetail;
use App\PaypalTransaction;
use App\ReachupPayment;
use App\TransactionDetail;
use App\User;
use App\UserNotification;
use App\UserReachup;
use Illuminate\Http\Request;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use App\Http\Resources\UserReachup as UserReachupResource;
use App\PaypalTransactionDetail;
use App\ReachupCoupon;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ReachupPaymentController extends BaseController
{

//     public function createPayment(Request $request)
//     {
//         $api = new \PayPal\Rest\ApiContext(
//             new \PayPal\Auth\OAuthTokenCredential(
//                 'AebW0-wKTfbvVmZS2ZWsltJCBwASRgThUSBFF8_1AYhxA29dJ_wn4yrJ-8auSADchSOJPNt1E1a_0aS-', //clientid
//                 'EGbiyNFJlZxsQmS84o0fQALx10ywlZZqaefPhfpYwrSm72om5W5CNHJyT0KEttM6Umyf8I461ntMkETl'  //secretid
//             )
//         );

//         // dd($api);
//         $reachup = UserReachup::find($reachup_id);
//         $charges = $reachup->charges;

//         // Create new payer and method
//         $payer = new Payer();
//         $payer->setPaymentMethod("paypal");

//         // // Set redirect urls
//         $redirectUrls = new RedirectUrls();
//         $redirectUrls->setReturnUrl("https://datanetdemo.com/reachup/reachup/public/")
//         ->setCancelUrl("https://datanetdemo.com/reachup/reachup/public/");

//         // Set payment amount
//         $amount = new Amount();
//         $amount->setCurrency("USD")
//         ->setTotal($charges);

//         // Set transaction object
//         $transaction = new Transaction();
//         $transaction->setAmount($amount)
//         ->setDescription("Payment description");

//         // dd($payer, $redirectUrls, $transaction);
//         // Create the full payment object
//         $payment = new Payment();
//         $payment->setIntent("authorize")
//         ->setPayer($payer)
//         ->setRedirectUrls($redirectUrls)
//         ->setTransactions(array($transaction));

//         // dd($payment->create($api));
//         $request = clone $payment;

//         try {
//             //code...
//             $payment->create($api);
//         } catch (\Throwable $th) {
//             //throw $th;
//             return $th;
//             exit(1);
//         }
//         return $payment;
//     }


//     public function executePayment(Request $request)
//     {
//         $api = new \PayPal\Rest\ApiContext(
//             new \PayPal\Auth\OAuthTokenCredential(
//                 'Ab4jgzEC2HP1YXs5j3YSE-DZLUXT5P44TlnOk4SPbHKtMB4lTs-Xkw0che7_mlicfnAq5bNou4XITfaC', //clientid
//                 'EGyGUS2uy_p5zp9yMEM2iSBKkBKET4KCIeM-rLeAO_w4Dp7qqE2EjeENheUnvFP5bqo9ry_9nHyGL5NI'  //secretid
//             )
//         );

    

//         $paymentId = $request->paymentID;
//         $payment = Payment::get($paymentId, $api);

//         $execution = new PaymentExecution();
//         $execution->setPayerId( $request->payerID);

// //        $transaction = new Transaction();
// //        $amount = new Amount();
// //
// //        $details = new Details();
// //        $details->setShipping($shippingCarges)
// ////            ->setTax(1.3)
// //            ->setShippingDiscount($dicount)
// //            ->setSubtotal($totalPrice);
// //
// //        $amount->setCurrency('GBP');
// //        $amount->setTotal($grandtotal-$dicount);
// //        $amount->setDetails($details);
// //        $transaction->setAmount($amount);
// //
// //        $execution->addTransaction($transaction);

//         try {
//             $result = $payment->execute($execution, $api);
//             $res = ['status'=>true,'payment_id'=>$result->id];
//             return $res;
//         } catch (\Throwable $ex) {
// //            exit(1);
//             $res = ['status'=>false,'error'=>$ex];
//             return $res;
//         }

//     }


    public function store(Request $request)
    {
        try {
            //code...
        
            $input = $request->all();
            $validator = Validator::make($input, [
                'card_no' => 'required',
                'title' => 'required',
                'expiry' => 'required',
                'cvc' => 'required',
                'card_type' => 'required',
                'reachup_id' => 'required|integer',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors()->first());       
            }

            if(PaypalTransaction::where('reachup_id', $input['reachup_id'])->exists()){
                return $this->sendError('Reachup payment already exist');       

            }

            $couponExist = false;
            $reachup_coupon = ReachupCoupon::where('user_reachup_id', $input['reachup_id'])->first();
            if($reachup_coupon){
                $couponExist = true;
            }
            
            // dd('**** **** **** '.(preg_replace( "#(.*?)(\d{4})$#", "$2", $request->card_no)));
            // dd($input);
            //paypal transaction get payment id
            $card_image = url('/').'/cards/paypal.png';
            $default_settings = DefaultSetting::first();

            $reachup = UserReachup::findOrFail($input['reachup_id']);
            $charges = $reachup->charges;
            $org_charges = $reachup->charges;
            $default_rules = DefaultRule::where('rule_type', 'default')->get();

            $advisor_percentage = $default_rules->where('concern', 'advisor')->first()->percentage ?? null;
            $reachup_percentage = $default_rules->where('concern', 'platform')->first()->percentage ?? null;
            $advisor_fee = ($charges*$advisor_percentage)/100;
            $reachup_fee = ($charges*$reachup_percentage)/100;

            $org_advisor_fee = $advisor_fee;
            $org_advisor_percentage = $advisor_percentage;

            if($couponExist){
                $advisor_percentage = intVal($advisor_percentage)-intVal($reachup_coupon->percentage);
                $advisor_fee = ($advisor_fee*$reachup_coupon->percentage)/100;
                $charges = $charges-(($charges*$reachup_coupon->percentage)/100);
            }
            // dd($charges);

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
            
            if(empty($result)) return $this->sendError('Error: No response');
            else
            {
                $json = json_decode($result);
                // dd($json);
                $token = $json->access_token;
            }
            
            curl_close($ch);

            $advisor = User::find($reachup->advisor_id);
            // dd($token);
            //replace email address with advisor email address
            // "email_address": "sb-an433c1305718@business.example.com"
            $data = '{
                "intent":"AUTHORIZE",
                "purchase_units":[{
                    "amount": {
                        "currency_code": "SGD",
                        "value": "'.$charges.'"
                    },
                    "payee": {
                        "email_address": "'.$advisor->paypal_email.'"
                    }
                }]
            }';
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
            if(empty($result)) return $this->sendError('Error: No response');
            else
            {
                $paypal_order = json_decode($result);
            }
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // dd($paypal_order->id);
            $links = $paypal_order->links;
            $key = array_search('approve', array_column($links, 'rel'));

            $link = $links[$key]->href;
            // dd($link);
            curl_close($ch);
            // dd($httpcode, $paypal_order);
            if($httpcode == 201){

                try {
                
                    DB::beginTransaction();
                    $paypal = PaypalTransaction::create([
                        'payment_id'   => $paypal_order->id, //replace with payment id from paypal
                        'reachup_id' => $request->reachup_id,
                        'card_no' => '**** **** **** '.(preg_replace( "#(.*?)(\d{4})$#", "$2", $request->card_no)),
                        'card_type' => $request->card_type,
                        'card_image' => $card_image,
                        'currency_code' => 'SGD',
                        'org_amount' => $org_charges,
                        'amount' => $charges,
                        'org_advisor_fee' => $org_advisor_fee,
                        'advisor_fee' => $advisor_fee,
                        'org_advisor_percentage' => $org_advisor_percentage,
                        'advisor_percentage' => $advisor_percentage,
                        'reachup_fee' => $reachup_fee,
                        'reachup_percentage' => $reachup_percentage,
                        'state' => 'intent',
                        'action_by' => auth()->user()->user_type_id,
                    ]);
                    $success = $this->create_transaction_detail($request->reachup_id, $paypal->id, 'intent', 'success');

                    $notification['title'] = 'Reachup Payment Intent';
                    $notification['message'] = 'Reachup payment has been intented';
                    $notification['type'] = 'payment'; 
                    $addtional_notification['reachup_id'] = $reachup->id;
                    $addtional_notification['advisor_id'] = $reachup->advisor_id;
                    $addtional_notification['user_id'] = $reachup->user_id;
                    $notification['additional'] = $addtional_notification;
                    $notification['user_id'] = $reachup->advisor_id;
                    $notification['created_by'] = auth()->user()->id;

                    $notify = UserNotification::notification([$reachup->advisor_id], $notification);
                    if($notify){
                        unset($notification['additional']);
                        $notification['user_reachup_id'] = $reachup->id;
                        $notification['notification'] = UserNotification::create($notification);
                    }
                    if(!$success){
                        DB::rollBack();
                        return $this->sendError('Transaction not created successfully');
                    } else {
                        DB::commit();
                    }
                    return $this->sendResponse(['link'=>$link], 'Payment intent authorize successfully.');
                } catch (\Throwable $th) {
                    DB::rollBack();
                    // dd($th);
                    if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }
                }
                
            } else {
                return $this->sendError('Http code not matched');

            }
        } catch (\Throwable $th) {
            // dd($th);
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }
        }
    }


    public function reachup_update(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'reachup_id' => 'required|integer',
                'status' => 'sometimes|nullable',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors()->first());       
            }

            $reachup = UserReachup::find($request->reachup_id);
            // dd($input);
            if(!$reachup){
                return $this->sendError('No reachup found.');
            }
            $old_status = $reachup->status;

            unset($input['reachup_id']);
            // $reachup->fill($input)->save();
            // if($input['status'] == '' || $input['status'] == null){
            //     $data['reachup'] = UserReachup::find($request->reachup_id);
            //     return $this->sendResponse(UserReachupResource::collection($data), 'User reachup status updated successfully.');
            // }

            /********** Reachup update status to process - newwork  ********/  
            // if($input['status'] == 'process'){
            //     if($old_status != 'process'){
            //         if(TransactionDetail::where('reachup_id', $request->reachup_id)->where('action', 'authorize')->where('status', 'success')->exists()){
            //             return $this->sendError('Payment already process');
            //         }
            //     }
            // } else 
            /********** Reachup update status to process - oldwork  ********/  
            // dd(isset($input['status']));
            if(isset($input['status'])){

                if($input['status'] == $old_status){
                    return $this->sendError('Reachup already in '.$old_status.' state');
                }
                if($input['status'] == 'process'){
                    if($old_status != 'process'){
                        if(TransactionDetail::where('reachup_id', $request->reachup_id)->where('action', 'authorize')->where('status', 'success')->exists()){
                            return $this->sendError('Payment already process');
                        }
                        if($old_status != 'pending'){
                            return $this->sendError('Reachup must be in pending state');
                        }
                        $a = $this->authorize_payment($request->reachup_id);
                        if(!$a->original['success']){
                            // dd($a);
                            if($a->original['message'] == 'Not Authorized'){
                                // User::where('id', $reachup->advisor_id)->update(['paypal_email'=>null]);
                                $patch_order = $this->patch_order($request->reachup_id);
                                if($patch_order->original['success']){
    
                                    $b = $this->authorize_payment($request->reachup_id);
                                    if(!$b->original['success']){
                                        return $this->sendError($b->original['message']);
                                    }
                                } else {
                                    return $this->sendError($patch_order->original['message']);
    
                                }
                                
                            } else {
    
                                return $this->sendError($a->original['message']);
                            }
                        }
                    }
                } else if ($input['status'] == 'reject'){
                    if($old_status != 'reject'){
                        if(TransactionDetail::where('reachup_id', $request->reachup_id)->where('action', 'void')->where('status', 'success')->exists()){
                            return $this->sendError('Payment already rejected');
                        }
                        if($old_status != 'process'){
                            return $this->sendError('Reachup must be in process state');
                        }
                        $a = $this->void($request->reachup_id);
                        if(!$a->original['success']){
    
                            return $this->sendError($a->original['message']);
                        }
                    }
                } else if ($input['status'] == 'accept'){
                    if($old_status != 'accept'){
                        if(TransactionDetail::where('reachup_id', $request->reachup_id)->where('action', 'capture')->where('status', 'success')->exists()){
                            return $this->sendError('Payment already accepted');
                        }
                        if($old_status != 'process'){
                            return $this->sendError('Reachup must be in process state');
                        }
                        $a = $this->capture($request->reachup_id);
                        if(!$a->original['success']){
    
                            return $this->sendError($a->original['message']);
                        }
                    }
                } else if ($input['status'] == 'cancel'){
                    if($old_status != 'cancel'){
                        if(TransactionDetail::where('reachup_id', $request->reachup_id)->where('action', 'cancel')->where('status', 'success')->exists()){
                            return $this->sendError('Payment already cancelled');
                        }
                        if($old_status != 'accept'){
                            return $this->sendError('Reachup must be in accepted state');
                        }
                        $a = $this->cancel($request->reachup_id);
                        if(!$a->original['success']){
    
                            return $this->sendError($a->original['message']);
                        }
                    }
                } else if ($input['status'] == 'completed'){
                    if($old_status != 'completed'){
                        // if(TransactionDetail::where('reachup_id', $request->reachup_id)->where('action', 'complete')->where('status', 'success')->exists()){
                        //     return $this->sendError('Payment already cancelled');
                        // }
                        if($old_status != 'accept'){
                            return $this->sendError('Reachup must be in accepted state');
                        }
                        $reachup_sd = $reachup->date.' '.$reachup->from_time;
                        $d = DateTime::createFromFormat('Y-m-d H:i:s', $reachup_sd);
                        if ($d === false) {
                            // die("Incorrect date string");
                        } else {
                            $reachup_timestamp =  $d->getTimestamp();
                        }

                        // $reachup_timestamp = Carbon::parse($reachup_sd)->timestamp;
                        $dateTime = new DateTime();
                        
                        $dateTime->setTimeZone(new DateTimeZone(auth()->user()->timezone));
                        $timezone = $dateTime->format('Y-m-d H:i:s');
                        // $current_timestamp = Carbon::parse($timezone)->timestamp;
                        $a = DateTime::createFromFormat('Y-m-d H:i:s', $timezone);
                        if ($a === false) {
                            //
                        } else {
                            $current_timestamp =  $a->getTimestamp();
                        }

                        if($reachup_timestamp > $current_timestamp){
                            return $this->sendError('Reachup time not started yet');
                        }
                        $a = $this->payout_item($request->reachup_id);
                        if(!$a->original['success']){
    
                            return $this->sendError($a->original['message']);
                        }
                    }
                }
            }
            // dd('out');

            $reachup->fill($input)->save();
            if(isset($input['status'])){

                if($reachup->status == 'accept' && $input['status'] == 'accept'){
                    //delete all previous pending crons
                    CronNotification::where('user_reachup_id', $reachup->id)->delete();

                    // $datetime = $reachup->date.' '.$reachup->from_time;
                    $reachup_sd = $reachup->date.' '.$reachup->from_time;
                    $parse_date = Carbon::parse($reachup_sd, auth()->user()->timezone)->setTimezone('UTC');
                    $datetime = $parse_date->toDateTimeString();

                    //create cron notification for before 24 hour notification 
                    $cronNotification1['user_id'] = $reachup->user_id;
                    $cronNotification1['advisor_id'] = $reachup->advisor_id;
                    $cronNotification1['datetime'] = date('Y-m-d H:i:s', strtotime($datetime. ' -1 day'));
                    $cronNotification1['before'] = '24 hours';
                    $cronNotification1['status'] = 'accept';
                    $cronNotification1['user_reachup_id'] = $reachup->id;

                    // dd($cronNotification1);
                    
                    //create cron notification for before 10 min notification 
                    $cronNotification2['user_id'] = $reachup->user_id;
                    $cronNotification2['advisor_id'] = $reachup->advisor_id;
                    $cronNotification2['datetime'] =date('Y-m-d H:i:s', strtotime($datetime. ' -10 minutes'));
                    $cronNotification2['before'] = '10 mins';
                    $cronNotification2['status'] = 'accept';
                    $cronNotification2['user_reachup_id'] = $reachup->id;

                    CronNotification::create($cronNotification1);
                    CronNotification::create($cronNotification2);

                } else if($reachup->status == 'reject' && $input['status'] == 'reject'){
                    //delete all previous pending crons
                    CronNotification::where('user_reachup_id', $reachup->id)->delete();
                }
            }
            $data['reachup'] = UserReachup::find($request->reachup_id);

            return $this->sendResponse(UserReachupResource::collection($data), 'User reachup status updated successfully.');
            

        } catch (\Throwable $th) {
            // dd($th);
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }
        }
    }

    public function patch_order($reachup_id)
    {
        $default_settings = DefaultSetting::first();
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
            
            if(empty($result)) return $this->sendError('Error: No response');
            else
            {
                $json = json_decode($result);
                // dd($json);
                $token = $json->access_token;
            }
            
            curl_close($ch);

        $paypal_transaction = PaypalTransaction::where('reachup_id', $reachup_id)->first();
        $charges = $paypal_transaction->amount;
        // $default = 'default';
        $path = "/purchase_units/@reference_id=='default'";
        $data = '[
            {
              "op": "replace",
              "path": "'.$path.'",
              "value": {
                  "amount": {
                      "currency_code": "SGD",
                      "value": '.$charges.'
                  }
                  
              }
            }
        ]';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v2/checkout/orders/".$paypal_transaction->payment_id);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array(
                            "Content-Type:application/json",
                            "Authorization:Bearer ".$token )
                );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        
        $result = curl_exec($ch);
        // $paypal_order = json_decode($result);
        // dd($paypal_order);
        if(empty($result)) return $this->sendError('Error: No response');
        else
        {
            return $this->sendResponse([], 'Order updated successfully');
        }
    }


    public function create_transaction_detail($reachup_id,$paypal_transaction_id, $action, $status)
    {
        try {
            $td = TransactionDetail::where('reachup_id', $reachup_id)->where('action', 'intent')->first();
            // $td->user
            TransactionDetail::updateOrCreate([
                'reachup_id' => $reachup_id,
                'action' => $action,
            ],[
                'paypal_transaction_id' => $paypal_transaction_id,
                'status' => $status,
                'user_fee' => $td->user_fee,
                'user_percentage' => $td->user_percentage,
                'advisor_fee' => $td->advisor_fee,
                'advisor_percentage' => $td->advisor_percentage,
                'reachup_fee' => $td->reachup_fee,
                'reachup_percentage' => $td->reachup_percentage,
            ]);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function update_transaction_detail($paypal_transaction_id, $action, $status)
    {
        try {
            if($action == 'cancel'){
                $pt = PaypalTransaction::find($paypal_transaction_id);
                if($pt){

                    TransactionDetail::where('paypal_transaction_id', $paypal_transaction_id)->where('action', $action)
                    ->update([
                        'status'=>$status,
                        'user_fee' => $pt->user_fee,
                        'user_percentage' => $pt->user_percentage,
                        'advisor_fee' => $pt->advisor_fee,
                        'advisor_percentage' => $pt->advisor_percentage,
                        'reachup_fee' => $pt->reachup_fee,
                        'reachup_percentage' => $pt->reachup_percentage,
                    ]);
                }
            } else {

                // dd($paypal_transaction_id, $action, $status);
                TransactionDetail::where('paypal_transaction_id', $paypal_transaction_id)->where('action', $action)
                ->update([
                    'status'=>$status
                ]);
            }
            PaypalTransaction::where('id', $paypal_transaction_id)->update(['state'=>$action,'action_by' => auth()->user()->user_type_id,]);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function create_paypal_transaction_detail($advisorFee, $payStatus, $advisorId, $paypalTransactionId)
    {
        try {
            PaypalTransactionDetail::create([
                'paypal_transaction_id'   => $paypalTransactionId, //replace with payment id from paypal
                'type' => 'advisor',
                'user_id' => $advisorId,
                'status' => $payStatus,
                'pay_amount' => $advisorFee,
            ]);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    
    
    public function update_paypal_transaction($paypalTransactionId, $user_percentage, $advisor_percentage, $reachup_percentage)
    {
        try {
            $paypal_transaction = PaypalTransaction::findOrFail($paypalTransactionId);
            $user_fee = ($paypal_transaction->amount*$user_percentage)/100;
            $advisor_fee = ($paypal_transaction->amount*$advisor_percentage)/100;
            $reachup_fee = ($paypal_transaction->amount*$reachup_percentage)/100;
            PaypalTransaction::where('id', $paypalTransactionId)->update([
                'user_fee'   => $user_fee, 
                'advisor_fee' => $advisor_fee,
                'reachup_fee' => $reachup_fee,
                'user_percentage'   => $user_percentage, 
                'advisor_percentage' => $advisor_percentage,
                'reachup_percentage' => $reachup_percentage,
                'action_by' => auth()->user()->user_type_id,
            ]);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }



    //new function 
    // public function authorize_payment($reachup_id, $authorization_id) 
    // {
    //     // dd($reachup_id);
    //     $reachup = UserReachup::find($reachup_id);

    //     if(!$reachup){
    //         return $this->sendError('No reachup found');
    //     }
    //     $payment = PaypalTransaction::where('reachup_id',$reachup_id)->first();
    //     if(!$payment){
    //         return $this->sendError('No payment to authorize');
    //     }

    //     $success = $this->create_transaction_detail($payment->reachup_id, $payment->id, 'authorize', 'initiate');

    //     if(!$success){
    //         return $this->sendError('Transaction not initiate successfully');
    //     }



    //     if( isset($authorization_id)){
    //         $payment->authorization_id = $authorization_id;
    //         $payment->update();


    //         $notification['title'] = 'Reachup Authorize';
    //         $notification['message'] = 'Your reachup payment has been authorize';
    //         $notification['type'] = 'payment';
    //         $notification['user_id'] = $reachup->user_id;
    //         $notification['created_by'] = auth()->user()->id;

    //         $notify = UserNotification::notification([$reachup->user_id], $notification);
    //         if($notify){
    //             $notification['user_reachup_id'] = $reachup->id;
    //             $notification['notification'] = UserNotification::create($notification);
    //         }

    //         $success = $this->update_transaction_detail($payment->id, 'authorize', 'success');
            
    //     } else {
    //         $success = $this->update_transaction_detail($payment->id, 'authorize', 'unsuccess');

    //     }
    //     return $this->sendResponse([], 'Payment authorize successfully');
        
    // }
    
    //old function
    public function authorize_payment($reachup_id) 
    {
        // dd($reachup_id);
        try {
            $reachup = UserReachup::find($reachup_id);

            if(!$reachup){
                return $this->sendError('No reachup found');
            }
            $payment = PaypalTransaction::where('reachup_id',$reachup_id)->first();
            if(!$payment){
                return $this->sendError('No payment to authorize');
            }
            // if(auth()->user()->id != intVal($reachup->advisor_id) && auth()->user()->id != intVal($reachup->user_id) ){
            if(auth()->user()->id != intVal($reachup->user_id) ){
                // dd(auth()->user()->id, $reachup->advisor_id);
                return $this->sendError('You are not allowed to authorize');
            }

            $success = $this->create_transaction_detail($payment->reachup_id, $payment->id, 'authorize', 'initiate');

            if(!$success){
                return $this->sendError('Transaction not initiate successfully');
            }



            $url= 'https://www.sandbox.paypal.com/checkoutnow?token='.$payment->payment_id;
            // dd($url);
            $default_settings = DefaultSetting::first();

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
            
            if(empty($result)) return $this->sendError('Error: No response');
            else
            {
                $json = json_decode($result);
                $token = $json->access_token;
            }
            
            curl_close($ch);
            // dd($token);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v2/checkout/orders/".$payment->payment_id."/authorize");
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER,array(
                                "Content-Type:application/json",
                                "Authorization:Bearer ".$token )
                    );
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($ch);
                    // dd($payment->payment_id);
            // dd($result, $ch);
            if(empty($result)) return $this->sendError('Error: No response');
            else
            {
                $paypal_order = json_decode($result);
            }
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            // dd($paypal_order, $httpcode);
            if($httpcode == 200 || $httpcode == 201){

                if( isset($paypal_order->status) && $paypal_order->status == 'COMPLETED'){
                    $authorization_id = $paypal_order->purchase_units[0]->payments->authorizations[0]->id;
                    $payment->authorization_id = $authorization_id;
                    $payment->update();


                    $notification['title'] = 'Reachup Authorize';
                    $notification['message'] = 'Your reachup payment has been authorize';
                    $notification['type'] = 'payment';
                    $addtional_notification['reachup_id'] = $reachup->id;
                    $addtional_notification['advisor_id'] = $reachup->advisor_id;
                    $addtional_notification['user_id'] = $reachup->user_id;
                    $notification['additional'] = $addtional_notification;
                    $notification['user_id'] = $reachup->user_id;
                    $notification['created_by'] = auth()->user()->id;

                    $notify = UserNotification::notification([$reachup->user_id], $notification);
                    if($notify){
                        unset($notification['additional']);
                        $notification['user_reachup_id'] = $reachup->id;
                        $notification['notification'] = UserNotification::create($notification);
                    }

                    $adv_notification['title'] = 'New Reachup Request';
                    $adv_notification['message'] = 'Congratulations. You have received a new reachup request';
                    $adv_notification['type'] = 'request';
                    $adv_addtional_notification['reachup_id'] = $reachup->id;
                    $adv_addtional_notification['advisor_id'] = $reachup->advisor_id;
                    $adv_addtional_notification['user_id'] = $reachup->user_id;
                    $adv_notification['additional'] = $adv_addtional_notification;
                    $adv_notification['user_id'] = $reachup->advisor_id;
                    $adv_notification['created_by'] = auth()->user()->id;

                    $adv_notify = UserNotification::notification([$reachup->advisor_id], $adv_notification);
                    if($adv_notify){
                        unset($adv_notification['additional']);
                        $adv_notification['user_reachup_id'] = $reachup->id;
                        $adv_notification['notification'] = UserNotification::create($adv_notification);
                    }

                    $success = $this->update_transaction_detail($payment->id, 'authorize', 'success');
                    
                } else {
                    $success = $this->update_transaction_detail($payment->id, 'authorize', 'unsuccess');

                }
                return $this->sendResponse([], 'Payment authorize successfully');
            } else {
                $success = $this->update_transaction_detail($payment->id, 'authorize', 'unsuccess');
                // dd($httpcode, $paypal_order);
                if($httpcode == 422 || $httpcode == 403) {
                    if($httpcode == 403){
                        if(isset($paypal_order)){

                            if($paypal_order->name == 'NOT_AUTHORIZED'){
                                $issue = isset($paypal_order->details[0]) ? $paypal_order->details[0]->issue : null;
                                if($issue == 'PAYEE_NOT_CONSENTED'){
                                    return $this->sendError('Paypal business account is required');
                                }
                                // return $this->sendError('Not Authorized');
                                // User::where('id', $reachup->advisor_id)->update(['paypal_email'=>null]);
                                // $this->authorize_payment($reachup_id);
                            } 
                        }
                    }
                    return $this->sendError(isset($paypal_order->details[0]) ? $paypal_order->details[0]->issue : $paypal_order->name);
                } else {
                    return $this->sendError('Http code not matched');
                }
            }
        } catch (\Throwable $th) {
            return $this->sendError('Error Occured: Due to '.$th->getMessage());
        }
        
    }

    public function show_order_detail($reachup_id)
    {
        $reachup = UserReachup::find($reachup_id);
        if(!$reachup){
            return $this->sendError('No reachup found');
        }
        //validate that user or advisor can check order detail
        // dd(auth()->user()->id, intVal($reachup->advisor_id), intVal($reachup->user_id));
        if(auth()->user()->id != intVal($reachup->advisor_id) && auth()->user()->id != intVal($reachup->user_id) ){
            return $this->sendError('You are not allowed to view this details');
        }
        $default_settings = DefaultSetting::first();

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
        
        if(empty($result)) return $this->sendError('Error: No response');
        else
        {
            $json = json_decode($result);
            $token = $json->access_token;
        }
        
        curl_close($ch);


        $payment = PaypalTransaction::where('reachup_id',$reachup_id)->first();
        if(!$payment){
            return $this->sendError('No payment created for this order');
        }
        // create curl resource
        $ch = curl_init();
        // set url
        curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v2/checkout/orders/".$payment->payment_id);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array(
                            "Content-Type:application/json",
                            "Authorization:Bearer ".$token )
                );
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //return the transfer as a string

        // $output contains the output string
        $output = curl_exec($ch);

        // dd($output);
        if(empty($output)) return $this->sendError('Error: No response');
        else
        {
            $paypal_order = json_decode($output);
        }
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // close curl resource to free up system resources
        curl_close($ch); 
        // dd($paypal_order);
        if($httpcode == 200){
            // if( isset($paypal_order->status) && $paypal_order->status == 'COMPLETED'){
            //     if($payment->authorization_id == null){
            //         if(isset($paypal_order->purchase_units[0]) && isset($paypal_order->purchase_units[0]->payments->authorizations[0])){

            //             $authorization_id = $paypal_order->purchase_units[0]->payments->authorizations[0]->id;
            //             $payment->authorization_id = $authorization_id;
            //             $payment->update();
            //             $success = $this->update_transaction_detail($payment->id, 'authorize', 'success');
            //         }
            //     }
            //     if($payment->transaction_id == null){
            //         if(isset($paypal_order->purchase_units[0]) && isset($paypal_order->purchase_units[0]->payments->captures[0])){
            //             $transaction_id = $paypal_order->purchase_units[0]->payments->captures[0]->id;
            //             $payment->transaction_id = $transaction_id;
            //             $payment->update();
            //             $success = $this->update_transaction_detail($payment->id, 'capture', 'success');
            //         }
            //     }
            // }
            return $this->sendResponse($paypal_order, 'Payment Response');
        } else {
            return $this->sendError('Http code not matched');

        }


    }

    public function void($reachup_id)
    {
        $reachup = UserReachup::find($reachup_id);
        if(!$reachup){
            return $this->sendError('No reachup found');
        }
        if($reachup->status == 'reject'){
            return $this->sendError('Reachup already has been rejected');
        }

        if($reachup->status != 'process'){
            return $this->sendError('Reachup is not in rejected state');
        }

        //validate that only user can void
        // if(auth()->user()->id != intVal($reachup->user_id) || auth()->user()->id != intVal($reachup->advisor_id)){
        //     return $this->sendError('You are not allowed to void');
        // } 
        // else {
        if(auth()->user()->id != intVal($reachup->user_id)){

            $noitfy_id = $reachup->advisor_id;
            $rejected_by = 'Advisor';
        } else if(auth()->user()->id != intVal($reachup->advisor_id)){
            $noitfy_id = $reachup->user_id;
            $rejected_by = 'User';
        } else {
            //validate that only user and advisor can void
            return $this->sendError('You are not allowed to void');
        }

        // }

        if(auth()->user()->id )

        $default_settings = DefaultSetting::first();
        $user_id = auth()->user()->id;
        // $reachup = $reachup->advisor_id;
        $payment_detail = User::where('id', $reachup->advisor_id)->first();
        // $merchant_id = $payment_detail->merchant_id;
        // if(!$merchant_id){
        //     return $this->sendError('Advisor paypal credentials missing');
        // }
        $paypal_email = $payment_detail->paypal_email;
        //  dd($paypal_email);
        $payment = PaypalTransaction::where('reachup_id',$reachup_id)->first();
        if(!$payment){
            return $this->sendError('No payment to void');
        }

        $success = $this->create_transaction_detail($payment->reachup_id, $payment->id, 'void', 'initiate');

        if(!$success){
            return $this->sendError('Transaction not initiate successfully');
        }
        
        // dd($advisor_id);
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
        
        if(empty($result)) return $this->sendError('Error: No response');
        else
        {
            $json = json_decode($result);
            $token = $json->access_token;
        }
        
        curl_close($ch);
        // if($paypal_email){

        //     /******** Paypal Auth Assertion Header ******/
        //     $header= json_encode([
        //         "alg" => "none"
        //     ]);
            
        //     $payload= json_encode([
        //         "email" => $paypal_email,
        //         // "payer_id" => $merchant_id,
        //         "iss" => $client_id,
        //     ]);
            
        //     // Encode Header
        //     $base64UrlHeader = $this->base64UrlEncode($header);
            
        //     // Encode Payload
        //     $base64UrlPayload = $this->base64UrlEncode($payload);
            
        //     $base64UrlSignature = "";
            
        //     $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
        //     // $headar_array ='"Content-Type:application/json",
        //     //     "Authorization:Bearer ".$token,
        //     //     "PayPal-Auth-Assertion:".$jwt';
        //     $headers = array(
        //         'Content-type: application/xml',
        //         'Authorization: Bearer '.$token.'',
        //         'PayPal-Auth-Assertion: '.$jwt.''
        //     );
        //     // $headar_array = '"Content-Type": "application/json", "Authorization": "Bearer '.$token.'", "PayPal-Auth-Assertion": "'.$jwt.'"';
        // } else {
        //     // $headar_array = '"Content-Type": "application/json", "Authorization": "Bearer '.$token.'"'; 
        // }
        $headers = array(
            'Content-type: application/xml',
            'Authorization: Bearer '.$token.''
        );

        // dd($headers);
        // create curl resource


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v2/payments/authorizations/".$payment->authorization_id."/void");
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers
                );
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        // $output contains the output stringz
        $output = curl_exec($ch);

        // dd($output);
        // if(empty($output)) return $this->sendError('Error: No response');
        // else
        // {
            // }
        $paypal_order = json_decode($output);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // close curl resource to free up system resources
        curl_close($ch); 
        // dd($paypal_order, $httpcode);
        if($httpcode == 204){
            $user_percentage = 100;
            $advisor_percentage = 0;
            $reachup_percentage = 0;
            $this->update_paypal_transaction($payment->id, $user_percentage, $advisor_percentage, $reachup_percentage);
            $success = $this->update_transaction_detail($payment->id, 'void', 'success');
            $notification['title'] = 'Reachup Rejected';
            $notification['message'] = 'Your reachup has been rejected';
            $notification['type'] = 'payment';
            $addtional_notification['reachup_id'] = $reachup->id;
            $addtional_notification['advisor_id'] = $reachup->advisor_id;
            $addtional_notification['user_id'] = $reachup->user_id;
            $notification['additional'] = $addtional_notification;
            $notification['user_id'] = $noitfy_id;
            $notification['created_by'] = auth()->user()->id;

            $notify = UserNotification::notification([$noitfy_id], $notification);
            if($notify){
                unset($notification['additional']);
                $notification['user_reachup_id'] = $reachup->id;
                $notification['notification'] = UserNotification::create($notification);
            }

            return $this->sendResponse([], 'Rejected Successfully');
        } else {
            $success = $this->update_transaction_detail($payment->id, 'void', 'unsuccess');
            if($httpcode == 422) {
                return $this->sendError(isset($paypal_order->details[0]) ? $paypal_order->details[0]->issue : $paypal_order->name);
            } else if($httpcode == 403) {
                return $this->sendError(isset($paypal_order->details[0]) ? $paypal_order->details[0]->issue : $paypal_order->name);
            } else if($httpcode == 400) {
                return $this->sendError(isset($paypal_order->error_description) ? $paypal_order->error_description : 'Error Occured');
            } else {
                return $this->sendError('Http code not matched');
            }
        }
    }

    public function base64UrlEncode($text)
    {
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($text)
        );
    }

    public function capture($reachup_id) 
    {
        $reachup = UserReachup::find($reachup_id);
        if(!$reachup){
            return $this->sendError('No reachup found');
        }
        //validate that only advisor can capture/accept
        if(auth()->user()->id != intVal($reachup->advisor_id) ){
            return $this->sendError('You are not allowed to accept');
        }

        if($reachup->status != 'process'){
            return $this->sendError('Reachup is not in accepted state');
        }

        $payment = PaypalTransaction::where('reachup_id',$reachup_id)->first();
        if(!$payment){
            return $this->sendError('No payment to capture');
        }

        $success = $this->create_transaction_detail($payment->reachup_id, $payment->id, 'capture', 'initiate');

        if(!$success){
            return $this->sendError('Transaction not initiate successfully');
        }

        // $url= 'https://www.sandbox.paypal.com/checkoutnow?token='.$payment->payment_id;
        // dd($url);
        $default_settings = DefaultSetting::first();

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
        
        if(empty($result)) return $this->sendError('Error: No response');
        else
        {
            $json = json_decode($result);
            $token = $json->access_token;
        }
        
        curl_close($ch);
        // dd($token);
        $data ='{
            "amount": {
              "value": "'.$payment->charged_amount.'",
              "currency_code": "'.$payment->currency_code.'"
            }
        }';
        // $data ='{
        //     "amount": {
        //       "value": "'.$payment->amount.'",
        //       "currency_code": "'.$payment->currency_code.'"
        //     },
        //     "final_capture": true,
        //       "payment_instruction": {
        //         "disbursement_mode": "INSTANT",
        //         "platform_fees": [{
        //             "amount": {
        //                 "currency_code": "'.$payment->currency_code.'",
        //                 "value": "'.$payment->reachup_fee.'"
        //             }
        //         }]
        //       }
        // }';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v2/payments/authorizations/".$payment->authorization_id."/capture");
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
        $result = curl_exec($ch);
                // dd($payment->payment_id);
                
        if(empty($result)) return $this->sendError('Error: No response');
        else
        {
            $paypal_order = json_decode($result);
            // dd($paypal_order);
        }
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // dd($paypal_order, $httpcode);
        // dd($paypal_order['purchase_units'][0]['payments']['authorization']);
        curl_close($ch);


        if($httpcode == 201){

            if( isset($paypal_order->status) && $paypal_order->status == 'COMPLETED'){
                // dd($paypal_order);
                $transaction_id = $paypal_order->id;
                $payment->transaction_id = $transaction_id;
                $payment->update();
                $success = $this->update_transaction_detail($payment->id, 'capture', 'success');

                $notification['title'] = 'Reachup Captured';
                $notification['message'] = 'Your reachup has been accepted';
                $notification['type'] = 'payment';
                $addtional_notification['reachup_id'] = $reachup->id;
                $addtional_notification['advisor_id'] = $reachup->advisor_id;
                $addtional_notification['user_id'] = $reachup->user_id;
                $notification['additional'] = $addtional_notification;
                $notification['user_id'] = $reachup->user_id;
                $notification['created_by'] = auth()->user()->id;

                $notify = UserNotification::notification([$reachup->user_id], $notification);
                if($notify){
                    unset($notification['additional']);
                    $notification['user_reachup_id'] = $reachup->id;
                    $notification['notification'] = UserNotification::create($notification);
                }
            }
            return $this->sendResponse($paypal_order, 'Payment Response');
        } else {
            $success = $this->update_transaction_detail($payment->id, 'capture', 'unsuccess');
            if($httpcode == 422) {
                return $this->sendError(isset($paypal_order->details[0]) ? $paypal_order->details[0]->issue : $paypal_order->name);
            } else {
                return $this->sendError('Http Code not matched');
            }
        }


        // dd(isset($paypal_order));
    }

    public function cancel($reachup_id)
    {
        try {
            $default_settings = DefaultSetting::first();
            $sender_id = auth()->user()->id;

            $reachup = UserReachup::find($reachup_id);
            if(!$reachup){
                return $this->sendError('No reachup found');
            }
            //validate that user or advisor can cancel
            if(auth()->user()->id != intVal($reachup->advisor_id) && auth()->user()->id != intVal($reachup->user_id) ){
                return $this->sendError('You are not allowed to cancel');
            }
            
            $payment = PaypalTransaction::where('reachup_id',$reachup_id)->first();
            if(!$payment){
                return $this->sendError('No payment to cancel');
            }

            $t=time();
            $sender_batch_id = 'advisor_'.$t.'_'.$sender_id;
            // dd($sender_batch_id);
            $user = User::find($reachup->user_id);
            $advisor = User::find($reachup->advisor_id);

            // $merchant_id = $advisor->merchant_id;
            // if(!$merchant_id){
            //     return $this->sendError('Advisor paypal credentials missing');
            // }

            $paypal_email = $advisor->paypal_email;

            $success = $this->create_transaction_detail($payment->reachup_id, $payment->id, 'cancel', 'initiate');

            if(!$success){
                return $this->sendError('Transaction not initiate successfully');
            }

            /*********** GET ADVISOR TOKEN **************/
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
            
            if(empty($result)) return $this->sendError('Error: No response');
            else
            {
                $json = json_decode($result);
                $token = $json->access_token;
            }
            
            curl_close($ch);
            /*********** GET ADVISOR TOKEN **************/



            


            /*********** GET PLATFORM TOKEN **************/
            $default_settings = DefaultSetting::first();

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
            
            if(empty($result)) return $this->sendError('Error: No response');
            else
            {
                $json = json_decode($result);
                $platform_token = $json->access_token;
            }
            
            curl_close($ch);
            /*********** GET PLATFORM TOKEN **************/

            // if($paypal_email){

            
            //     /******** Paypal Auth Assertion Header ******/
            //     $header= json_encode([
            //         "alg" => "none"
            //     ]);
                
            //     $payload= json_encode([
            //         "email" => $paypal_email,
            //         // "payer_id" => $merchant_id,
            //         "iss" => $client_id,
            //     ]);

            //     // Encode Header
            //     $base64UrlHeader = $this->base64UrlEncode($header);

            //     // Encode Payload
            //     $base64UrlPayload = $this->base64UrlEncode($payload);

            //     $base64UrlSignature = "";

            //     $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
            //     $headers = array(
            //         'Content-Type: application/json',
            //         'Authorization: Bearer '.$platform_token.'',
            //         'PayPal-Auth-Assertion: '.$jwt.'' 
            //     );
            // } else {
            //     $headers = array(
            //         'Content-type: application/json',
            //         'Authorization: Bearer '.$platform_token.''
            //     );
                
            //     // array(
            //         //     "Content-Type:application/json",
            //         //     "Authorization: Bearer ".$platform_token,
            //         //     "PayPal-Auth-Assertion:".$jwt )
            //         // $headar_array = '"Content-Type": "application/json", "Authorization": "Bearer '.$token.'"'; 
            // }
            $headers = array(
                'Content-type: application/json',
                'Authorization: Bearer '.$platform_token.''
            );
            // dd($headers);
            $default_rules = DefaultRule::get();
            if($payment->state != "capture"){
                return $this->sendError('Payment state must be in capture');
            }

            /* Calculate hour of reachup schedule date */
            $current_date = date('Y-m-d');
            $current_time = date('H:i:s');
            $schedule_date = $reachup->date;
            $schedule_time = $reachup->from_time;
            $date1 = "$current_date $current_time";
            $date2 = "$schedule_date $schedule_time";
            // dd($date1, $date2);
            $timestamp1 = strtotime($date1);
            $timestamp2 = strtotime($date2);
            $hour = abs($timestamp2 - $timestamp1)/(60*60);
            /* Calculate hour of reachup schedule date */


            $user_default_rules = $default_rules->where('action_by', 'user')->where('rule_type', 'cancel');
            $advisor_default_rules = $default_rules->where('action_by', 'advisor')->where('rule_type', 'cancel');
            // if($hour >= 48){
            //         dd('in');
            // } else {
            //     dd('out');
            // }
            // dd($user, $sender_id);
            /* When user cancel reachup after acceptance */
            $advisorFee = 0;
            $payStatus = 'paid';
            if($user->id == $sender_id){
                // dd('1');
                //check all default rules of user
                $user_percentage = 0;
                $advisor_percentage = 0;
                $reachup_percentage = 0;
                foreach ($user_default_rules as $key => $value) {
                    //if cancel before 48 hours left to schedule time
                    // dd( $value, $hour, floatval($value->hour));
                    if($value->approximately == 'before' && floatval($value->hour) < $hour ){
                        // dd($value);
                        if($value->concern == 'user'){
                            $user_percentage = $value->percentage;
                        } else if($value->concern == 'platform') { 
                            $reachup_percentage = $value->percentage;
                        }
                        //refund amount to user according to percentage
                        if($value->concern == 'user'){
                            // dd($reachup->charges);
                            $pay_amount = ($reachup->charges*$value->percentage)/100;
                            $data = '{
                                "amount": {
                                    "currency_code": "'.$payment->currency_code.'",
                                    "value": "'.$pay_amount.'"
                                }
                            }';
                            // dd($data, $pay_amount);
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v2/payments/captures/".$payment->transaction_id."/refund");
                            curl_setopt($ch, CURLOPT_VERBOSE, true);
                            curl_setopt($ch, CURLOPT_HEADER, 1);
                            curl_setopt($ch, CURLOPT_FAILONERROR, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
                            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);  
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        
                            // $output contains the output string
                            $output = curl_exec($ch);
                            curl_errno($ch);
                            // dd($output, curl_errno($ch), curl_error($ch));
                            if(empty($output)) return $this->sendError('Error: No response');
                            else
                            {
                                $paypal_order = json_decode($output);
                                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            }
                            // dd($output, $httpcode, $paypal_order);
                            // close curl resource to free up system resources
                            curl_close($ch);
                        } 
                        
                    } 
                    //if cancel when less than 48 hours left to schedule time
                    else if($value->approximately == 'after' && $value->hour >= $hour){
                        // dd('in');
                        if($value->concern == 'user'){
                            $user_percentage = $value->percentage;
                        } else if($value->concern == 'platform') { 
                            $reachup_percentage = $value->percentage;
                        } else if($value->concern == 'advisor') { 
                            $advisor_percentage = $value->percentage;
                        }
                        if($value->concern == 'user'){
                            // dd($value);
                            $pay_amount = ($reachup->charges*$value->percentage)/100;
                            $data = '{
                                "amount": {
                                    "currency_code": "'.$payment->currency_code.'",
                                    "value": "'.$pay_amount.'"
                                }
                            }';
                            // dd($data, $pay_amount);
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v2/payments/captures/".$payment->transaction_id."/refund");
                            curl_setopt($ch, CURLOPT_VERBOSE, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
                            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);  
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        
                            // $output contains the output string
                            $output = curl_exec($ch);
        
                            if(empty($output)) return $this->sendError('Error: No response');
                            else
                            {
                                $paypal_order = json_decode($output);
                                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            }
                            // dd($output, $httpcode, $paypal_order);
                            // close curl resource to free up system resources
                        } 
                        //refund amount to advisor according to percentage
                        if($value->concern == 'advisor'){
                            // dd($value);
                            $pay_amount = ($reachup->charges*$value->percentage)/100;
                            $advisorFee = $pay_amount;
                            // $data = '{
                            //     "amount": {
                            //       "currency_code": "'.$payment->currency_code.'",
                            //       "value": "'.$pay_amount.'"
                            //     }
                            // }';
        
                            /****************** SHOULD CHANGE RECIEVER EMAIL WITH ADVISOR EMAIL ****************/
                            // i.e $advisor->email
                            if($paypal_email){
                                $penaltyCharges = abs(floatval($advisor->penalty_charges));
                                $pay_amount = abs($pay_amount);
                                if($advisor->penalty_charges != 0){
                                    if($pay_amount < $penaltyCharges){
                                        $remaining_penalty = $penaltyCharges - $pay_amount;
                                        $advisor->penalty_charges = $remaining_penalty;
                                        $advisor->update();

                                        $pay_amount = 0;
                                    } else {
                                        $pay_amount = $pay_amount - $penaltyCharges;
                                        $advisor->penalty_charges = 0;
                                        $advisor->update();
                                    }
                                }
                                $data = '{
                                    "sender_batch_header": {
                                    "sender_batch_id": "'.$sender_batch_id.'",
                                    "email_subject": "You have a payout!",
                                    "email_message": "You have received a payout! Thanks for using our service!"
                                    },
                                    "items": [
                                    {
                                        "recipient_type": "EMAIL",
                                        "amount": {
                                        "currency": "'.$payment->currency_code.'",
                                        "value": "'.$pay_amount.'"
                                        },
                                        "note": "Thanks for your patronage!",
                                        "receiver": "'.$paypal_email.'"
                                    
                                    }
                                
                                    ]
                                }';
                            } else {
                                $payStatus = 'unpaid';
                            }
                            $cptd = $this->create_paypal_transaction_detail($advisorFee, $payStatus, $reachup->advisor_id, $payment->id);

                            // dd($data, $pay_amount);
                            if($paypal_email){
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/payments/payouts");
                                curl_setopt($ch, CURLOPT_VERBOSE, true);
                                curl_setopt($ch, CURLOPT_HTTPHEADER,array(
                                                    "Content-Type:application/json",
                                                    "Authorization:Bearer ".$platform_token )
                                        );
                                curl_setopt($ch, CURLOPT_POST, true);
                                curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
                                curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);  
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            
                                // $output contains the output string
                                $output = curl_exec($ch);
            
                                if(empty($output)) return $this->sendError('Error: No response');
                                else
                                {
                                    $paypal_order = json_decode($output);
                                    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                }
                                // dd($output, $httpcode, $paypal_order);
                                // close curl resource to free up system resources
                                curl_close($ch);
                            }
                            
                        } 
                        // dd($value);
                        // dd('b');
                    }
                }
                $this->update_paypal_transaction($payment->id, $user_percentage, $advisor_percentage, $reachup_percentage);

                // dd('loop_end');
                $notification['title'] = 'Reachup Cancel';
                $notification['message'] = 'Your reachup has been cancel';
                $notification['type'] = 'payment';
                $addtional_notification['reachup_id'] = $reachup->id;
                $addtional_notification['advisor_id'] = $reachup->advisor_id;
                $addtional_notification['user_id'] = $reachup->user_id;
                $notification['additional'] = $addtional_notification;
                $notification['user_id'] = $reachup->advisor_id;
                $notification['created_by'] = auth()->user()->id;

                $notify = UserNotification::notification([$reachup->advisor_id], $notification);
                if($notify){
                    unset($notification['additional']);
                    $notification['user_reachup_id'] = $reachup->id;
                    $notification['notification'] = UserNotification::create($notification);
                }
            } 

            /* When advisor cancel reachup after acceptance */
            else if($advisor->id == $sender_id){
                //check all default rules of advisor
                $user_percentage = 0;
                $advisor_percentage = 0;
                $reachup_percentage = 0;
                foreach ($advisor_default_rules as $key => $value) {
                    // dd($hour);
                    //if cancel before 48 hours left to schedule time
                    if($value->approximately == 'before' && $value->hour < $hour ){
                        // dd('a');
                        if($value->concern == 'user'){
                            $user_percentage = $value->percentage;
                        } else if($value->concern == 'advisor') { 
                            $advisor_percentage = $value->percentage;
                        }

                        //refund amount to user according to percentage
                        if($value->concern == 'user'){
                            $pay_amount = ($reachup->charges*$value->percentage)/100;
                            $data = '{
                                "amount": {
                                    "currency_code": "'.$payment->currency_code.'",
                                    "value": "'.$pay_amount.'"
                                }
                            }';
                            // dd($data, $pay_amount);
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v2/payments/captures/".$payment->transaction_id."/refund");
                            curl_setopt($ch, CURLOPT_VERBOSE, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
                            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);  
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        
                            // $output contains the output string
                            $output = curl_exec($ch);
        
                            if(empty($output)) return $this->sendError('Error: No response');
                            else
                            {
                                $paypal_order = json_decode($output);
                                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            }
                            // dd($output, $httpcode, $paypal_order);
                            // close curl resource to free up system resources
                            curl_close($ch);
                        }
                        //charge penalty to advisor according to percentage
                        if($value->concern == 'advisor'){
                            $penalty_amount = ($reachup->charges*$value->percentage)/100;
                            $advisor->penalty_charges += abs($penalty_amount);
                            $advisor->update();
                        }
                    } 
                    //if cancel when less than 48 hours left to schedule time
                    else if($value->approximately == 'after' && $value->hour >= $hour){
                        if($value->concern == 'user'){
                            $user_percentage = $value->percentage;
                        } else if($value->concern == 'advisor') { 
                            $advisor_percentage = $value->percentage;
                        }

                        //refund amount to user according to percentage
                        if($value->concern == 'user'){
                            // dd('user');
                            $pay_amount = ($reachup->charges*$value->percentage)/100;
                            $data = '{
                                "amount": {
                                    "currency_code": "'.$payment->currency_code.'",
                                    "value": "'.$pay_amount.'"
                                }
                            }';
                            // dd($data, $pay_amount);
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v2/payments/captures/".$payment->transaction_id."/refund");
                            curl_setopt($ch, CURLOPT_VERBOSE, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
                            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);  
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        
                            // $output contains the output string
                            $output = curl_exec($ch);
        
                            if(empty($output)) return $this->sendError('Error: No response');
                            else
                            {
                                $paypal_order = json_decode($output);
                                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            }
                            // dd($output, $httpcode, $paypal_order);
                            // close curl resource to free up system resources
                            curl_close($ch);
                        }
                        //charge penalty to advisor according to percentage
                        if($value->concern == 'advisor'){
                            
                            $penalty_amount = ($reachup->charges*$value->percentage)/100;
                            $advisor->penalty_charges += abs($penalty_amount);
                            $advisor->update();
                            // dd($penalty_amount);

                        }
                    }
                }
                $this->update_paypal_transaction($payment->id, $user_percentage, $advisor_percentage, $reachup_percentage);

                $notification['title'] = 'Reachup Cancel';
                $notification['message'] = 'Your reachup has been cancel';
                $notification['type'] = 'payment';
                $addtional_notification['reachup_id'] = $reachup->id;
                $addtional_notification['advisor_id'] = $reachup->advisor_id;
                $addtional_notification['user_id'] = $reachup->user_id;
                $notification['additional'] = $addtional_notification;
                $notification['user_id'] = $reachup->user_id;
                $notification['created_by'] = auth()->user()->id;

                $notify = UserNotification::notification([$reachup->user_id], $notification);
                if($notify){
                    unset($notification['additional']);
                    $notification['user_reachup_id'] = $reachup->id;
                    $notification['notification'] = UserNotification::create($notification);
                }
            }
            $success = $this->update_transaction_detail($payment->id, 'cancel', 'success');

            return $this->sendResponse([], 'Cancel Successfully');

            
        } catch (\Throwable $th) {
            //throw $th;
            $success = $this->update_transaction_detail($payment->id, 'cancel', 'unsuccess');

            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured : Due to '. $th->getMessage());
            }
        }
    }

    public function payout_item($reachup_id) 
    {
        $reachup = UserReachup::find($reachup_id);
        if(!$reachup){
            return $this->sendError('No reachup found');
        }
        //validate that user or advisor can payout
        if(auth()->user()->id != intVal($reachup->advisor_id) && auth()->user()->id != intVal($reachup->user_id) ){
            return $this->sendError('You are not allowed to payout');
        }

        $advisor = User::find($reachup->advisor_id);
        $t=time();
        $super_admin = User::where('is_superadmin', 1)->first()->id ?? 0;
        $sender_batch_id = 'advisor_'.$t.'_'.$super_admin;
        $paypal_email = $advisor->paypal_email;
        $payment = PaypalTransaction::where('reachup_id',$reachup_id)->first();
        if(!$payment){
            return $this->sendError('No payment to payout');
        }

        $success = $this->create_transaction_detail($payment->reachup_id, $payment->id, 'complete', 'initiate');

        if(!$success){
            return $this->sendError('Transaction not initiate successfully');
        }


        $url= 'https://www.sandbox.paypal.com/checkoutnow?token='.$payment->payment_id;
        // dd($url);
        $default_settings = DefaultSetting::first();

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
        
        if(empty($result)) return $this->sendError('Error: No response');
        else
        {
            $json = json_decode($result);
            $token = $json->access_token;
        }
        
        curl_close($ch);
        // dd($payment->transaction_id);
        // $data ='{
        //     "reference_id": "'.$payment->transaction_id.'",
        //     "reference_type": "TRANSACTION_ID"
        //   }';

        $payStatus = 'paid';
        $pay_amount = abs(floatval($payment->advisor_fee));
        $penaltyCharges = abs($advisor->penalty_charges);
        if($penaltyCharges != 0){
            if($pay_amount < $penaltyCharges){
                $remaining_penalty = $penaltyCharges - $pay_amount;
                $advisor->penalty_charges = $remaining_penalty;
                $advisor->update();

                $pay_amount = 0;
            } else {
                $pay_amount = $pay_amount - $penaltyCharges;
                $advisor->penalty_charges = 0;
                $advisor->update();
            }
        }
        $advisorFee = $pay_amount;
        // dd($pay_amount, $penaltyCharges, $advisor->penalty_charges);
        
        if($paypal_email){
            $data = '{
                "sender_batch_header": {
                "sender_batch_id": "'.$sender_batch_id.'",
                "email_subject": "You have a reachup payment!",
                "email_message": "You have received a reachup payment! Thanks for using our service!"
                },
                "items": [
                {
                    "recipient_type": "EMAIL",
                    "amount": {
                    "currency": "'.$payment->currency_code.'",
                    "value": "'.$pay_amount.'"
                    },
                    "note": "Thanks for your advising to our customers!",
                    "receiver": "'.$paypal_email.'"
                
                }
            
                ]
            }';
        } else {
            $httpcode = 201;
            $payStatus = 'unpaid';
        }

        // dd($headers);

        if($paypal_email){
            // // dd($data);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/payments/payouts");
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
            $result = curl_exec($ch);
            // dd($result);
            if(empty($result)) return $this->sendError('Error: No response');
            else
            {
                $paypal_order = json_decode($result);
            }
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);
        }
        if($httpcode == 422){
            $issue = isset($paypal_order->details[0]) ? $paypal_order->details[0]->issue : $paypal_order->name;
            if($issue == 'INSUFFICIENT_FUNDS'){
                $httpcode = 201;
                $payStatus = 'unpaid';
            }
        }
        $cptd = $this->create_paypal_transaction_detail($advisorFee, $payStatus, $reachup->advisor_id, $payment->id);

        if($httpcode == 201){

            // if( isset($paypal_order->processing_state) && $paypal_order->processing_state->status == 'SUCCESS'){
                // $payment->status = 'Completed';
                // $payment->update();
                if(!$paypal_email)
                    $success = $this->update_transaction_detail($payment->id, 'complete', 'unsuccess');
                else 
                    $success = $this->update_transaction_detail($payment->id, 'complete', 'success');

                $notification['title'] = 'Reachup Complete';
                $notification['message'] = 'Your reachup has been completed';
                $notification['type'] = 'payment';
                $addtional_notification['reachup_id'] = $reachup->id;
                $addtional_notification['advisor_id'] = $reachup->advisor_id;
                $addtional_notification['user_id'] = $reachup->user_id;
                $notification['additional'] = $addtional_notification;
                $notification['user_id'] = $reachup->user_id;
                $notification['created_by'] = auth()->user()->id;

                $super_admin = User::where('is_superadmin', 1)->first()->id ?? 0;
                $advisor = User::find($reachup->advisor_id);
                $user = User::find($reachup->user_id);
                if($advisor){
                    $user_feedback_notification['title'] = 'Feedback to '.$advisor->name;
                    $user_feedback_notification['message'] = 'Remember to give feedback on how '.$advisor->name.' performed in the reachup';
                    $user_feedback_notification['type'] = 'rating';
                    $user_feedback_notification['target_id'] = $reachup->advisor_id;
                    $addtional_user_feedback_notification['reachup_id'] = $reachup->id;
                    $addtional_user_feedback_notification['advisor_id'] = $reachup->advisor_id;
                    $addtional_user_feedback_notification['user_id'] = $reachup->user_id;
                    $user_feedback_notification['additional'] = $addtional_user_feedback_notification;
                    $user_feedback_notification['user_id'] = $reachup->user_id;
                    $user_feedback_notification['created_by'] = $super_admin;

                    $type = 'complete_reachup';
                    $default_settting = DefaultSetting::first();
                    if($default_settting->is_additional_charges){
                        $type = 'complete_reachup_with_additional_charges';
                    }

                    $email_template = EmailTemplate::where('type', $type)->first();
                    if($email_template){

                        $messageTemplate = $email_template->html;
                        $search = ['&lt;&lt;receiver&gt;&gt;','&lt;&lt;sender&gt;&gt;','&lt;&lt;reachup_subject&gt;&gt;','&lt;&lt;from_time&gt;&gt;','&lt;&lt;to_time&gt;&gt;','&lt;&lt;charges&gt;&gt;'];
                        $replace = [' '.$user->name,' '.$advisor->name,' '.$reachup->reachup_subject,' '.$reachup->from_time,' '.$reachup->to_time,' '.$reachup->charges];
                        if($default_settting->is_additional_charges){
                            $search = ['&lt;&lt;receiver&gt;&gt;','&lt;&lt;sender&gt;&gt;','&lt;&lt;reachup_subject&gt;&gt;','&lt;&lt;from_time&gt;&gt;','&lt;&lt;to_time&gt;&gt;','&lt;&lt;charges&gt;&gt;','&lt;&lt;handling_title&gt;&gt;','&lt;&lt;handling_fee&gt;&gt;','&lt;&lt;handling_percentage&gt;&gt;','&lt;&lt;paid_charges&gt;&gt;'];
                            $replace = [' '.$user->name,' '.$advisor->name,' '.$reachup->reachup_subject,' '.$reachup->from_time,' '.$reachup->to_time,' '.$reachup->charges,' '.$reachup->handling_title,' '.$reachup->handling_charges,' '.$reachup->handling_percentage,' '.$reachup->paid_charges];
                        }
                        $messageTemplateHtml = str_replace($search,$replace,$messageTemplate);
                        if(\App::environment() == 'production')
                            Mail::send('emails.complete_reachup', ['messageTemplateHtml' => $messageTemplateHtml],function ($m) use ($user) {
                                $m->to($user->email)->subject('Reachup Completed');
                            });   
                    }         
                    $notify_user = UserNotification::notification([$reachup->user_id], $user_feedback_notification);
                    if($notify_user){
                        unset($user_feedback_notification['additional']);
                        $user_feedback_notification['user_reachup_id'] = $reachup->id;
                        UserNotification::create($user_feedback_notification);
                    }
                }

                if($user){
                    $advisor_feedback_notification['title'] = 'Feedback to '.$user->name;
                    $advisor_feedback_notification['message'] = 'Remember to give feedback on how '.$user->name.' performed in the reachup';
                    $advisor_feedback_notification['type'] = 'rating';
                    $advisor_feedback_notification['target_id'] = $reachup->user_id;
                    $addtional_advisor_feedback_notification['reachup_id'] = $reachup->id;
                    $addtional_advisor_feedback_notification['advisor_id'] = $reachup->advisor_id;
                    $addtional_advisor_feedback_notification['user_id'] = $reachup->user_id;
                    $advisor_feedback_notification['additional'] = $addtional_advisor_feedback_notification;
                    $advisor_feedback_notification['user_id'] = $reachup->advisor_id;
                    $advisor_feedback_notification['created_by'] = $super_admin;
                    $notify_advisor = UserNotification::notification([$reachup->advisor_id], $advisor_feedback_notification);
                    
                    $email_template = EmailTemplate::where('type', 'complete_reachup')->first();
                    if($email_template){
                        $messageTemplate = $email_template->html;
                        $search = ['&lt;&lt;receiver&gt;&gt;','&lt;&lt;sender&gt;&gt;','&lt;&lt;reachup_subject&gt;&gt;','&lt;&lt;from_time&gt;&gt;','&lt;&lt;to_time&gt;&gt;','&lt;&lt;charges&gt;&gt;'];
                        $replace = [' '.$advisor->name,' '.$user->name,' '.$reachup->reachup_subject,' '.$reachup->from_time,' '.$reachup->to_time,' '.$reachup->charges];
                        $messageTemplateHtml = str_replace($search,$replace,$messageTemplate);
                        
                        if(\App::environment() == 'production')
                        Mail::send('emails.complete_reachup', ['messageTemplateHtml' => $messageTemplateHtml],function ($m) use ($advisor) {
                            $m->to($advisor->email)->subject('Reachup Completed');
                        });
                    }
                    if($notify_advisor){
                        unset($advisor_feedback_notification['additional']);
                        $advisor_feedback_notification['user_reachup_id'] = $reachup->id;
                        UserNotification::create($advisor_feedback_notification);
                    }
                    $advisor_feedback_notification['user_reachup_id'] = $reachup->user_reachup_id;
                }

                $notify = UserNotification::notification([$reachup->user_id], $notification);
                if($notify){
                    unset($notification['additional']);
                    $notification['user_reachup_id'] = $reachup->id;
                    $notification['notification'] = UserNotification::create($notification);
                }

                return $this->sendResponse([], 'Payment payout successfully');
            // } else {
            //     return $this->sendError('Something went wrong');
            // }
            // dd($paypal_order->processing_state->status, $httpcode, 'success');

        } else {
            // dd($paypal_order, $httpcode, 'failure');
            $success = $this->update_transaction_detail($payment->id, 'complete', 'unsuccess');

            if($httpcode == 422) {
                return $this->sendError(isset($paypal_order->details[0]) ? $paypal_order->details[0]->issue : $paypal_order->name);
            } else {
                // dd($paypal_order);
                return $this->sendError(isset($paypal_order->name) ? $paypal_order->name : 'Http Code not matched');
            }
        }
    }

    
}
