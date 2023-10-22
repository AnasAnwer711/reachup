<?php

namespace App\Console\Commands;

use App\CronLog;
use App\CronNotification;
use App\DefaultSetting;
use App\PaypalTransaction;
use App\TransactionDetail;
use App\User;
use App\UserDevice;
use App\UserNotification;
use App\UserReachup;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ReachupNotify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reachup:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send push notification when user session ends';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function cron_logs($message)
    {
        # create logs if success or error comes
        $data = [
            'message' => $message,
        ]; 
        CronLog::create($data);
    }

    public function secondsToWords($reachupTime) {

        $now = time();
        $seconds = $now - $reachupTime;
        /** number of days **/
        $days = intval($seconds / 86400);
        
        /** number of hours **/
        $hours = intval((($seconds - ($days * 86400)) / 3600));
        /** number of mins **/
        $mins = intval((($seconds - $days * 86400 - $hours * 3600) / 60));
        /** number of seconds **/
        // $secs = intval(($seconds - ($days * 86400) - ($hours * 3600) - ($mins * 60)));
        
        $hours = abs($hours);
        $mins = abs($mins);
    
        /** if more than one day **/
        $day_plural = $days > 1 ? 'days' : 'day';
        /** if more than one hour **/
        $hour_plural = $hours > 1 ? 'hours' : 'hour';
        /** if more than one min **/
        $min_plural = $mins > 1 ? 'mins' : 'min';
        /** return the string **/
        // return sprintf("%d $plural, %d hours, %d min, %d sec", $days, $hours, $mins, $secs);
        if($days > 0)
            return sprintf("%d $day_plural %d $hour_plural %d $min_plural", $days,$hours,$mins);
        else 
            return sprintf("%d $hour_plural %d $min_plural", $hours,$mins);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            //code...
            
            // DB::enableQueryLog();
            $qry = CronNotification::where('is_notified', 0)->where('datetime','<=', date('Y-m-d H:i:s'));
            $cron_notifications = $qry->get();
            // dd($cron_notifications);
            // $queries = DB::getQueryLog();
            if(count($cron_notifications)){

                foreach ($cron_notifications as $cron_notification) { 
                    $notify_date = DateTime::createFromFormat('Y-m-d H:i:s',  $cron_notification->datetime);
                    if($notify_date){
                        $timestamp = $notify_date->getTimestamp();
                        $secondsToWords = $this->secondsToWords($timestamp);
                    } else {
                        $this->cron_logs('Incorrect datetime found in Cron Notify ID: '.$cron_notification->id);
                    }

                    $message = '';
                    if($cron_notification->before == '5 hours'){
                        $message = 'You have only '.$secondsToWords.' left to accept Reachup Session';
                    } else if($cron_notification->before == '24 hours'){
                        $message = 'Your Reachup Session will start in next '.$secondsToWords.'';
                    } else {
                        $message = 'Your Reachup Session is about to start in '.$secondsToWords.' approx';
                    }

                    $after_message_for_advisor = '';
                    $after_message_for_user = '';
                    if($cron_notification->after == '1 minute'){
                        $after_message_for_advisor = 'Your reachup has been rejected as you didn`t respond on time, this will make impact on your rating.';
                        $after_message_for_user = 'Your reachup has been rejected as advisor didn`t respond on time.';

                    }
                    $super_admin = User::where('is_superadmin', 1)->first()->id ?? 0;
// dd($after_message_for_advisor, $after_message_for_user);
                    if($cron_notification->status == 'pending'){
                        if($cron_notification->after == '1 minute'){
                            //function for reject
                            $reject = $this->rejectReachup($cron_notification->user_reachup_id);
                            if(!$reject['success']){
                                $this->cron_logs($reject['message']);
                            } else {
                                UserReachup::where('id', $cron_notification->user_reachup_id)->update(['status'=> 'reject']);
                            }

                            //Notification for user
                            $user_notification = $this->makeNotifyData('Reachup rejected', $after_message_for_user, $cron_notification->user_id, $cron_notification->user_reachup_id, $super_admin);
                            // $this->notification($cron_notification->user_id, $user_notification);
                            UserNotification::notification([$cron_notification->user_id], $user_notification);
                            UserNotification::create($user_notification);


                            //Notification for advisor
                            $advisor_notification = $this->makeNotifyData('Reachup rejected', $after_message_for_advisor, $cron_notification->advisor_id, $cron_notification->user_reachup_id, $super_admin);
                            // $this->notification($cron_notification->advisor_id, $advisor_notification);
                            UserNotification::notification([$cron_notification->advisor_id], $advisor_notification);
                            UserNotification::create($advisor_notification);
                        }

                        //Notification for advisor
                        $advisor_notification = $this->makeNotifyData('Reachup is pending', $message, $cron_notification->advisor_id, $cron_notification->user_reachup_id, $super_admin);
                        // $this->notification($cron_notification->advisor_id, $advisor_notification);
                        UserNotification::notification([$cron_notification->advisor_id], $advisor_notification);
                        UserNotification::create($advisor_notification);
                    } else {

                        //Notification for user 
                        $user_notification = $this->makeNotifyData('Reachup to Start', $message, $cron_notification->user_id, $cron_notification->user_reachup_id, $super_admin);
                        // $this->notification($cron_notification->user_id, $user_notification);
                        UserNotification::notification([$cron_notification->user_id], $user_notification);
                        UserNotification::create($user_notification);

                        //Notification for advisor
                        $advisor_notification = $this->makeNotifyData('Reachup to Start', $message, $cron_notification->advisor_id, $cron_notification->user_reachup_id, $super_admin);
                        // $this->notification($cron_notification->advisor_id, $advisor_notification);
                        UserNotification::notification([$cron_notification->advisor_id], $advisor_notification);
                        UserNotification::create($advisor_notification);
                    }
                    $user_devices = UserDevice::where('user_id', $cron_notification->user_id)->pluck('fcm_token')->toArray();
                    array_walk($user_devices, function(&$value, $key) { $value = 'u-'.$value; } );
                    $notify_tokens = implode(",", $user_devices);
                    $advisor_devices = UserDevice::where('user_id', $cron_notification->advisor_id)->pluck('fcm_token')->toArray();
                    array_walk($advisor_devices, function(&$value, $key) { $value = 'a-'.$value; } );
                    $notify_tokens .= ',';
                    $notify_tokens .= implode(",", $advisor_devices);
                    
                    $cron_notification->update([
                        'is_notified' => 1,
                        'notified_datetime' => date('Y-m-d H:i:s'),
                        'notify_tokens' => $notify_tokens,
                    ]);

                    $this->cron_logs('Cron runs successfully for Reachup ID: '.$cron_notification->user_reachup_id.' Cron Notify ID: '.$cron_notification->id);
                }
            } else {
                $this->cron_logs('No cron to run');
            }

            // $qry->delete();
            echo 'Notify Successfully';
        } catch (\Throwable $th) {
            //throw $th;
            $this->cron_logs('Error cause due to: '.$th->getMessage());
            echo $th->getMessage();
        }
    }

    public function create_transaction_detail($reachup_id,$paypal_transaction_id, $action, $status)
    {
        try {
            TransactionDetail::updateOrCreate([
                'reachup_id' => $reachup_id,
                'action' => $action,
            ],[
                'paypal_transaction_id' => $paypal_transaction_id,
                'status' => $status,
            ]);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function update_transaction_detail($paypal_transaction_id, $action, $status)
    {
        try {
            // dd($paypal_transaction_id, $action, $status);
            $this->cron_logs('Update Transaction Detail with status: '.$status);
            $this->cron_logs('on paypal transaction id: '.$paypal_transaction_id);
            $this->cron_logs('Action: '.$action);

            TransactionDetail::where('paypal_transaction_id', $paypal_transaction_id)->where('action', $action)->update(['status'=>$status]);
            PaypalTransaction::where('id', $paypal_transaction_id)->update(['state'=>$action]);
            $this->cron_logs('Transaction Detail Updated Successfully');
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function rejectReachup($reachup_id)
    {
        $reachup = UserReachup::find($reachup_id);
        if(!$reachup){
            $response = [
                'success' => false,
                'message' => 'No reachup found',
            ];
            return $response;
        } 
        
        $default_settings = DefaultSetting::first(); 

        $payment_detail = User::where('id', $reachup->advisor_id)->first();
 
        $paypal_email = $payment_detail->paypal_email;
        //  dd($paypal_email);
        $payment = PaypalTransaction::where('reachup_id',$reachup_id)->first();
        if(!$payment){
            $response = [
                'success' => false,
                'message' => 'No payment to void',
            ];
            return $response;
        }
        if(!$payment->authorization_id){
            $response = [
                'success' => false,
                'message' => 'Paypal order not authorized by user',
            ];
            return $response;
        }

        $success = $this->create_transaction_detail($payment->reachup_id, $payment->id, 'void', 'initiate');

        if(!$success){
            $response = [
                'success' => false,
                'message' => 'Transaction not initiate successfully',
            ];
            return $response;
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
        //     $headers = array(
        //         'Content-type: application/xml',
        //         'Authorization: Bearer '.$token.''
        //     );
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
        $super_admin = User::where('is_superadmin', 1)->first()->id ?? 0;

        if($httpcode == 204){
            $success = $this->update_transaction_detail($payment->id, 'void', 'success');
            $this->cron_logs('Notification for reject');
           
            $notification1['title'] = 'Reachup Rejected';
            $notification1['message'] = 'Your reachup has been rejected';
            $notification1['type'] = 'payment';
            $addtional_notification1['reachup_id'] = $reachup->id;
            $addtional_notification1['advisor_id'] = $reachup->advisor_id;
            $addtional_notification1['user_id'] = $reachup->user_id;
            $notification1['additional'] = $addtional_notification1;
            $notification1['user_id'] = $reachup->advisor_id;
            $notification1['created_by'] = $super_admin;

            $notification2['title'] = 'Reachup Rejected';
            $notification2['message'] = 'Your reachup has been rejected';
            $notification2['type'] = 'payment';
            $addtional_notification2['reachup_id'] = $reachup->id;
            $addtional_notification2['advisor_id'] = $reachup->advisor_id;
            $addtional_notification2['user_id'] = $reachup->user_id;
            $notification2['additional'] = $addtional_notification2;
            $notification2['user_id'] = $reachup->user_id;
            $notification2['created_by'] = $super_admin;

            $this->cron_logs('Notify to advisor');
            // $this->cron_logs('Notify object of Advisor: '.$notification1);
            // $this->cron_logs('Notify object of User: '.$notification2);
            $this->cron_logs('Notify to advisor id: '.$reachup->advisor_id);
            $notify1 = UserNotification::notification([$reachup->advisor_id], $notification1);

            $this->cron_logs('notify1 result: '.$notify1);
            $notify2 = UserNotification::notification([$reachup->user_id], $notification2);
            if($notify1){
                unset($notification1['additional']);
                $notification1['user_reachup_id'] = $reachup->id;
                UserNotification::create($notification1);
            }
            if($notify2){
                unset($notification2['additional']);
                $notification2['user_reachup_id'] = $reachup->id;
                UserNotification::create($notification2);
            }
            $response = [
                'success' => true,
                'message' => 'Rejected Successfully',
            ];
            return $response;
        } else {
            $success = $this->update_transaction_detail($payment->id, 'void', 'unsuccess');
            $message = '';
            if(isset($paypal_order->error_description)){
                $message = $paypal_order->error_description;
            } else if(isset($paypal_order->details[0])){
                $message = $paypal_order->details[0]->description;
            } else {
                $message = 'Error Occured';
            }
            $response = [
                'success' => false,
                'message' => $message,
            ];
            return $response;
            // if($httpcode == 422) {
            //     // return $this->sendError(isset($paypal_order->details[0]) ? $paypal_order->details[0]->issue : $paypal_order->name);
            // } else if($httpcode == 403) {
            //     // return $this->sendError(isset($paypal_order->details[0]) ? $paypal_order->details[0]->issue : $paypal_order->name);
            // } else if($httpcode == 400) {
            //     // return $this->sendError(isset($paypal_order->error_description) ? $paypal_order->error_description : 'Error Occured');
            // } else {
            //     $response = [
            //         'success' => false,
            //         'message' => 'Transaction not initiate successfully',
            //     ];
            //     return $response;
                // return $this->sendError('Http code not matched');
            // }
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

    public function makeNotifyData($title, $message, $user_id, $reachup_id, $created_by)
    {
        $data['title'] = $title;
        $data['message'] = $message;
        $data['type'] = 'request';
        $data['user_id'] = $user_id;
        $data['created_by'] = $created_by;
        $data['user_reachup_id'] = $reachup_id;
        return $data;
    }

    public function notification($user_id, $input)
    {
        // dd($input);
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

        $notification = [
            'title' => $input['title'] ?? '',
            'body' => $input['message'] ?? '',
            'tag' => $input['type'] ?? '',
            'image' => $input['image'] ?? '',
            'sound' => true,
        ];
        
        // $extraNotificationData = ["message" => $notification,"moredata" =>'dd'];
        $fcmNotification = [
            //'registration_ids' => $tokenList, //multple token array
            'registration_ids'  => $user_id, //single token
            'notification'      => $notification,
            'data'              => $notification
        ];
        $headers = [
            'Authorization: key=AAAArFdZnE0:APA91bEz8XiikTslMSKxjTA-lKWU94EOGTP_my3PH6M7eX1uYBN1QAyQOp5YX_mbK4OzkXcCK1ZtQHA-HgKbVmBYvG70yHCfWk__YD4Pu43GGhvfCYvJ-KLrbVUgAqEEEp1WiECgodIe',
            'Content-Type: application/json'
        ];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);
    }
}
