<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $guarded = [];

    public function getCreatedAtAttribute($value)
    {
        $timezone = 'Asia/Singapore';
        $user = User::find($this->user_id);
        if($user){
            if(isset($user->timezone))
                $timezone = $user->timezone;
        } 
        $formatted_date = date('Y-m-d H:i:s',strtotime($value));
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $formatted_date);
        $date->setTimezone($timezone);
        return $date->toDateTimeString();
    }
    
    public static function notification($user_ids, $input)
    {
        if(!is_array($user_ids)){
            return false;
        }
        // dd($user_ids, $input);
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        // $token=$token;
        // $token='dzJ1pgFxQaGfJ2QZRBAzFT:APA91bEWaf3tl1-zMIYMZNLsnNMi3DkaDfTm7fb0zaMAwDZz9ogPkZCee2ryMeiOzECWBOlMSwiBh3n8YxYYJ7I2xmNrySK7jX5qVl81CIGldvxL0TP-_bJFLSJjx0osRAYJ0YOxHApj';
        foreach ($user_ids as $key => $value) {
            $tokenList = [];
            $findUser = User::findOrFail($value);
            $findDevices = $findUser->devices;
            if(count($findDevices)){
                foreach ($findDevices as $key => $device) {
                    # code...
                    $tokenList[] = $device->fcm_token;
                }
            }
        }

        // dd($tokenList);
        $notification = [
            'title' => $input['title'] ?? '',
            'body' => $input['message'] ?? '',
            'addtional' => $input['additional'] ?? null,
            'tag' => $input['type'] ?? '',
            'image' => $input['image'] ?? '',
            'sound' => true,
        ];
        
        // $extraNotificationData = ["message" => $notification,"moredata" =>'dd'];
        $fcmNotification = [
            //'registration_ids' => $tokenList, //multple token array
            'registration_ids'  => $tokenList, //single token
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
        // dd($result);
        return true;
    }
}
