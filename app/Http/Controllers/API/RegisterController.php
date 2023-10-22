<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource as UserResource;
use App\Http\Resources\UserKeyword as UserKeywordResource;
use App\Http\Resources\ReachupPayment as ReachupPaymentResource;
use App\PasswordReset;
use App\PaypalTransaction;
use App\UserDevice;
use App\UserNotification;
use App\UserReachup;
use App\UserSocialToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Http\Resources\UserSocialToken as UserSocialTokenResource;

class RegisterController extends BaseController
{
    public function register(Request $request)
    {
        // dd($request->all());
        try {
            //code...

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|social_login_exist|unique:users,email',
                'username' => 'required|unique:users,username|regex: /^[a-zA-Z0-9_]*$/',
                'password' => 'required',
                // 'c_password' => 'required|same:password',
                'phone' => 'required',
                'phone_code' => 'required|in:pk,sg,us',
                'image' => 'sometimes|nullable|mimes:jpeg,png,jpg,gif,svg|max:8192',
            ],
            [
                'image.mimes' => 'The image must be file of type jpeg,png,jpg,gif,svg',
                'email.social_login_exist' => 'Your account already associated with social login'
            ]);
                
                
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors()->first());       
            }

            // if ($request->country_code != "sg" || $request->country_code != "pk" || $request->country_code != "us"){
            //     return $this->sendError('Phone number is invalid');
            // }

            $input = $request->all();
            $input['image'] = null;
            if($request->hasFile('image'))
            {
                $image = $request->file('image');
                $name = time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('/images');
                // dd(public_path);
                
                $image->move($destinationPath, $name);
                $input['image'] = url('/').'/images/'.$name;
            }
            // dd($input);
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
            $success['token'] =  $user->createToken('reachup')->accessToken;
            // $success['username'] =  $user->username;
            $success['user'] =  new UserResource($user);
            $success['user']['keywords'] =  UserKeywordResource::collection($user->keywords);
            $token = Str::random(60);
            // $emailTemplate = view('emails.confirmation_email',[ 'user' => $user]);
            // $messagetemp = $emailTemplate->render();
            
            // $to = $request->email;
            // $subject = "Confirmation Email";
            // // Always set content-type when sending HTML email
            // $headers[] = "MIME-Version: 1.0";
            // $headers[] .= "Content-type:text/html;charset=UTF-8";

            // // More headers
            // $headers[] .= 'From: Reachup <no-reply@reachup.com>';
            // mail($to,$subject,$messagetemp, implode("\r\n", $headers));
            if(\App::environment() == 'production')
                Mail::send('emails.confirmation_email', ['user' => $user], function ($m) use ($user) {
                    $m->to($user->email, $user->name)->subject('Confirmation Email');
                });

            return $this->sendResponse($success, 'User register successfully.');
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
     * Login api
     *
     * @return \Illuminate\Http\Response
     */

    public function checkEmail($email)
    {
        $find1 = strpos($email, '@');
        $find2 = strpos($email, '.',$find1);
        return ($find1 !== false && $find2 !== false && $find2 > $find1);
    }

    public function login(Request $request)
    {
        try {
            //code...
            $validator = Validator::make($request->all(), [
                'user' => 'required',
                'password' => 'required',
                'fcm_token' => 'required',
            ]);
            
            
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors()->first());       
            }
            // dd(User::all());
            if ( $this->checkEmail($request->user) ) {
                $attempt = Auth::attempt(['email' => $request->user, 'password' => $request->password]);
            } else {
                $attempt = Auth::attempt(['username' => $request->user, 'password' => $request->password]);
            }
            // dd($request->all());
            if($attempt){ 
                $user = Auth::user(); 
                if($user->user_type_id == 3){
                    Auth::logout();
                    // return $this->sendError('You are not authorized to login from app, Kindly visit https://reachup.us to proceed');
                    return $this->sendError('This account can’t be used for app login since this has been associated with admin dashboard');
                }
                if(!$user->email_verified_at){
                    Auth::logout();
                    return $this->sendError('Please verify your email address. If you didn’t receive the email ,then press resend button for re-request',[],401);
                }
                if($user->status == 'blocked'){
                    Auth::logout();
                    return $this->sendError('Your account has been blocked temporary, please contact with administrator to unblock your account');
                }

                if(isset($request->timezone)){
                    $user->timezone = $request->timezone;
                }

                $user->update();

                UserDevice::where('user_id', '!=', Auth::user()->id)
                ->where('fcm_token', $request->fcm_token)->delete();
                
                UserDevice::updateOrCreate([
                    'user_id'   => Auth::user()->id,
                    'fcm_token' => $request->fcm_token,
                ],[
                    'device_id' => $request->device_id,
                    'last_access'    => date('Y-m-d H:i:s')
                ]);
                $success['token'] =  $user->createToken('reachup')->accessToken; 
                $success['user'] =  new UserResource($user);
                $success['user']['keywords'] =  UserKeywordResource::collection($user->keywords);
                // dd($success);
                return $this->sendResponse($success, 'User login successfully.');
            } 
            else{ 
                if( $this->checkEmail($request->user)){
                    $user = User::where('email', $request->user)->first();
                    if($user && $user->password == null){
                        $st = [];
                        foreach ($user->social_tokens as $key => $value) {
                            $st[] = $value->login_type;
                        }
                        $login_type = implode(',', $st);
                        return $this->sendError('This email is associated with social account(s) of '.$login_type.'. Please continue with social account');
                    }
                }
                return $this->sendError('Email or password is incorrect');
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

    public function social_login(Request $request)
    {
        try {
            
        
            $validator = Validator::make($request->all(), [
                'social_token' => 'required',
                'login_type' => 'required',
                // 'fcm_token' => 'required',
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors()->first());       
            }

            $userExist = User::where('email',$request->email)->first();
            $userToken = UserSocialToken::where('social_token', $request->social_token)->first();

            if($userToken){
                $userExist = User::find($userToken->user_id);
            }
            
            $email = isset($request->email) ? $request->email : ($userExist ? $userExist->email : null);


                // dd($userExist);
            $user = User::updateOrCreate([
                'email'   => $email,
                
            ],[
                'name' => isset($request->name) ? $request->name : ($userExist ? $userExist->name : null),
                // 'social_url' => isset($request->social_url) ? $request->social_url : ($userExist ? $userExist->social_url : null),
                // 'phone' => isset($request->phone) ? $request->phone : ($userExist ? $userExist->phone : null),
                'address' => isset($request->address) ? $request->address : ($userExist ? $userExist->address : null),
                'image' => isset($request->image) ? $request->image : ($userExist ? $userExist->image : null),
                'profile_complete' => $userExist ? $userExist->profile_complete : 0,
                'email_verified_at' => $userExist ? $userExist->email_verified_at : now(),
            ]);
            UserDevice::updateOrCreate([
                'user_id'   => $user->id,
                'fcm_token' => $request->fcm_token,
            ],[
                'device_id' => $request->device_id,
                'last_access'    => date('Y-m-d H:i:s')
            ]);
            if(UserSocialToken::where('user_id', $user->id)->where('login_type', $request->login_type)->doesntExist()){

                $social_token  = UserSocialToken::create([
                    'user_id'   => $user->id,
                    'social_token' => $request->social_token,
                    'social_url' => $request->social_url,
                    'login_type' => $request->login_type,
                    ]);
            } else {
                UserSocialToken::where('user_id', $user->id)->where('login_type', $request->login_type)->update(['social_token'=>$request->social_token, 'social_url' => $request->social_url ?? null]);
            }

            // dd($social_token);
            $success['token'] =  $user->createToken('reachup')->accessToken;
            $success['user'] =  new UserResource($user);
            $success['user']['keywords'] =  UserKeywordResource::collection($user->keywords);
            // dd($success);


            return $this->sendResponse($success, 'User registered successfully.');
        } catch (\Throwable $th) {
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }
        }
    }



    public function logout()
    { 
        try {
            if (Auth::check()) {
                Auth::user()->token()->revoke();
                return $this->sendResponse(null, 'User logged out successfully.');
            }   
        } catch (\Throwable $th) {
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }
        }
    }

    public function profile_index()
    {
        try {
            
            $data['user'] = new UserResource(auth()->user());
            return $this->sendResponse($data, 'Profile retrieved successfully.');
        } catch (\Throwable $th) {
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }
        }
    }

    public function profile_stats()
    {
        try {
            $user_reachups = UserReachup::where('user_id', auth()->user()->id)->get();
            $advisor_reachups = UserReachup::where('advisor_id', auth()->user()->id)->get();
            // $data['total_amount_spent'] = UserReachup::where('user_id', auth()->user()->id)->get()->sum('charges') ?? 0;
            // $data['total_amount_earned'] = UserReachup::where('advisor_id', auth()->user()->id)->get()->sum('charges') ?? 0;
            $data['spent'] = [];
            $data['earned'] = [];
            // dd($reachups);
            $data['total_amount_spent'] = 0;
            $data['total_amount_earned'] = 0;
            foreach ($user_reachups as $key => $value) {
                $payment = PaypalTransaction::where('reachup_id', $value->id)->first();

                if($payment->state != 'intent')
                    $data['total_amount_spent'] += $payment->amount ?? 0;
                if($payment->state == 'cancel' || $payment->state == 'void'){

                    $data['total_amount_spent'] = $data['total_amount_spent']-$payment->user_fee ?? 0;
                }
                // dd($value);
                $payment['payType'] = 'spent';
                if ($payment){
                    // if($payment->user_fee > 0)
                    if($payment->state != 'intent')
                        $data['spent'][] = new ReachupPaymentResource($payment);
                }
            }
            foreach ($advisor_reachups as $key => $value) {
                // dd($value);
                $payment = PaypalTransaction::where('reachup_id', $value->id)->first();
                if($payment->state == 'complete' || $payment->state == 'capture'){

                    $data['total_amount_earned'] += $payment->advisor_fee ?? 0;
                    $payment['payType'] = 'earned';
                    if ($payment){
                        if($payment->advisor_fee > 0)
                            $data['earned'][] = new ReachupPaymentResource($payment);
                    }
                }
            }
            return $this->sendResponse($data, 'Profile stats retrieved successfully.');
        } catch (\Throwable $th) {
            return $this->sendError('Error Occured.');
            //throw $th;
        }
        // dd($data);
    }

    public function profile_store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes',
                'email' => 'sometimes|unique:users,email,'.auth()->user()->id,
                'username' => 'sometimes|regex: /^[a-zA-Z0-9_]*$/|unique:users,username,'.auth()->user()->id,
                'phone' => 'sometimes|required_with:phone_code',
                'phone_code' => 'required_with:phone|in:pk,sg,us',
                'image' => 'sometimes|mimes:jpeg,png,jpg,gif,svg|max:8192',
            ],
            [
                'image.mimes' => 'The image must be file of type jpeg,png,jpg,gif,svg'
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors()->first());       
            }
            $input = $request->all();
            $user = User::where('id', auth()->user()->id)->first();
                // dd($user->image);
                // dd(parse_url($user->image)['path']); 
            if($request->hasFile('image'))
            {
                // dd($user->image);
                $usersImage = public_path(parse_url($user->image)['path']);
                // $usersImage = public_path("images/{$user->image}"); // get previous image from folder
                if (File::exists($usersImage)) { // unlink or remove previous image from folder
                    if(isset($user->image)){
                        unlink($usersImage);
                    }
                }
                // dd($user);
                $image = $request->file('image');
                $name = time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('/images');
                // dd(public_path);
        
                $image->move($destinationPath, $name);
                $input['image'] = url('/').'/images/'.$name;
            }
            $input['profile_complete'] = 1;
            $user->fill($input)->save();
            $success['user'] =  new UserResource($user);
            // dd($input);
            return $this->sendResponse($success, 'User Updated successfully.');
        } catch (\Throwable $th) {
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }
        }
       
    }

    public function update_image(Request $request)
    {
        try {
            
            $validator = Validator::make($request->all(), [
                'image' => 'required|mimes:jpeg,png,jpg,gif,svg|max:8192',
            ],
            [
                'image.mimes' => 'The image must be file of type jpeg,png,jpg,gif,svg'
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors()->first());       
            }
            $input = $request->all();
            $user = User::where('id', auth()->user()->id)->first();

            if($request->hasFile('image'))
            {
                $usersImage = null;
                if($user->image){
                    $usersImage = public_path(parse_url($user->image)['path']);
                }
                if (File::exists($usersImage)) { // unlink or remove previous image from folder
                    unlink($usersImage);
                }
                $image = $request->file('image');
                $name = time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('/images');
                // dd(public_path);
        
                $image->move($destinationPath, $name);
                $input['image'] = url('/').'/images/'.$name;
            }

            $user->fill($input)->save();
            $success['user'] =  new UserResource($user);
            // dd($input);
            return $this->sendResponse($success, 'User Updated successfully.');
        } catch (\Throwable $th) {
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }
        }
       
    }
    
    public function update_paypal_email(Request $request)
    {
        try {
            
            $validator = Validator::make($request->all(), [
                'paypal_email' => 'required|unique:users,paypal_email,'.auth()->user()->id,
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors()->first());       
            }
            $input = $request->all();
            $user = User::where('id', auth()->user()->id)->first();
            if($input['paypal_email']){
                $input['is_payment_detail_completed'] = 1;
            }
            $user->fill($input)->save();
            $success['user'] =  new UserResource($user);
            // dd($input);
            Mail::send('emails.update_paypal_email', ['user' => $user], function ($m) use ($user) {
                $m->to($user->email, $user->name)->subject('Link for Paypal Transactions');
            });

            return $this->sendResponse($success, 'Paypal email updated successfully.');
        } catch (\Throwable $th) {
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }
        }
       
    }

    public function update_social_token(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'social_token' => 'required',
                'login_type' => 'required',
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors()->first());       
            }
            $input = $request->all();
            $user = User::where('id', auth()->user()->id)->first();

            $input['user_id'] = auth()->user()->id;

            UserSocialToken::updateOrCreate([
                'user_id'   => $input['user_id'],
                'login_type'   => $input['login_type'],
            ],[
                'social_token' => $input['social_token'],
                'social_url' => $input['social_url'] ?? null
            ]);
            
            $success['user'] =  new UserResource($user);
            return $this->sendResponse($success, 'User Updated successfully.');

        } catch (\Throwable $th) {
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }
        }
       
    }

    public function social_token($user_id)
    {
        try {
            $user_social_token =  UserSocialToken::where('user_id', $user_id)->get();
            if(count($user_social_token)){
                $success['social_token'] = UserSocialTokenResource::collection($user_social_token);
                return $this->sendResponse($success, 'User social token retrieved.');

            } else {
                return $this->sendError('No social token exists for this user');
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

    public function forgot_password(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required',
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors()->first());       
            }
            // $input = $request->all();
            $user = User::where('email', $request->email)->where(function($q){
                $q->where('user_type_id',1)->orWhere('user_type_id',2);
            })->first();
            // dd($user);
            if(!$user){
                return $this->sendError('Email not found');
            } else {
                $password_reset = PasswordReset::where('email' , $request->email)->first();
                if($password_reset){
                    PasswordReset::where('email' , $request->email)->delete();
                }
                $token = Str::random(60);
                PasswordReset::insert([
                    'email' => $request->email,
                    'token' => Hash::make($token),
                ]);

                // $emailTemplate = view('emails.password_reset',[ 'user' => $user,'token' => $token]);
                // $messagetemp = $emailTemplate->render();
                
                // $to = $request->email;
                // $subject = "Reset Password";
                // // Always set content-type when sending HTML email
                // $headers[] = "MIME-Version: 1.0";
                // $headers[] .= "Content-type:text/html;charset=UTF-8";

                // // More headers
                // $headers[] .= 'From: Reachup <no-reply@reachup.com>';
                // mail($to,$subject,$messagetemp, implode("\r\n", $headers));
                
                Mail::send('emails.password_reset', ['user' => $user,'token' => $token], function ($m) use ($user) {
                    //$m->from('no_reply@botanicalgarden.patronassist.com', 'Museum');
                    $m->to($user->email, $user->name)->subject('Reset Password');
                });
                
            }

            return $this->sendResponse([], 'Email has been sent successfully.');

        } catch (\Throwable $th) {
            // dd($th);
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }
        }
    }

    public function reset_my_password($token = null)
    {
        // dd($token);
        $rows = PasswordReset::get();
        $tokenMismatch = true;
        if(count($rows)){

            foreach($rows as $row){
                if(Hash::check($token, $row->token)){
                    // dd('in');
                    $tokenMismatch = false;
                    break;
                }
                
            }
        }
        // dd($tokenMismatch);
        if($tokenMismatch || count($rows) < 1)
            return view('auth.password_reset')->with(['token' => $token, 'error'=>'This password reset token is invalid', 'success'=>'']);

        return view('auth.password_reset')->with(
            ['token' => $token, 'error'=>'', 'success'=>'']
        );
    }


    public function reset_password_request(Request $request, $token = null)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'password' => 'required|confirmed',
		    // 'old_password' => 'required|different:password',
        ]);
        if($validator->fails()){
            return back()->with('error',$validator->messages()->first());
        }
        $a = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );
        // dd($a);
        // $user = User::where('email', $request->email)->where(function($q){
        //     $q->where('user_type_id',1)->orWhere('user_type_id',2);
        // })->first();
        $user = User::where('email', $request->email)->first();

        // $user = User::where('email', $request->email)->first();
        // dd($user);
        if(!$user){
            // dd($a['token']);
            // return redirect('reset_my_password/'.$token);
            return redirect('reset_my_password/'.$a['token'])->with(
                ['token' => $a['token'], 'error'=>'We cannot find a user with that e-mail address', 'success'=>'']
            );
            
        }
        // dd($request->email);
        $rows = PasswordReset::get();
        $tokenMismatch = true;
        if(count($rows)){

            foreach($rows as $row){
                // dd($a['token'], $row);
                if(Hash::check($a['token'], $row->token)){
                    $tokenMismatch = false;
                    break;
                }
                
            }
        }
        // dd($tokenMismatch);
        if($tokenMismatch || count($rows) < 1)
            return redirect('reset_my_password/'.$a['token'])->with(
                ['token' => $a['token'], 'error'=>'This password reset token is invalid.', 'success'=>'']
            );

        $pr_user =  PasswordReset::where('email', $request->email)->first();
        // dd($pr_user);

        if(!$pr_user)
            return redirect('reset_my_password/'.$a['token'])->with(
                ['token' => $a['token'], 'error'=>'This password reset token is invalid.', 'success'=>'']
            );

        // $emailMatched = false;
        $tokenMismatch2 = true;
        if(Hash::check($a['token'], $pr_user->token)){
            $tokenMismatch2 = false;
            // break;
        }
   
        if(!$tokenMismatch2){
            $user->password = Hash::make($a['password']);

            $user->setRememberToken(Str::random(60));
            $user->save();
            // If the user shouldn't reuse the token later, delete the token 
            PasswordReset::where('email', $a['email'])->delete();

            Mail::send('emails.password_changed', [ 'user' => $user], function ($m) use ($user) {
                //$m->from('no_reply@botanicalgarden.patronassist.com', 'Museum');
                $m->to($user->email, $user->name)->subject('Password Changed');
            });

            // $emailTemplate = view('emails.password_changed',[ 'user' => $user]);
            // $messagetemp = $emailTemplate->render();
            
            // $to = $request->email;
            // $subject = "Confirmation Email";
            // // Always set content-type when sending HTML email
            // $headers[] = "MIME-Version: 1.0";
            // $headers[] .= "Content-type:text/html;charset=UTF-8";

            // // More headers
            // $headers[] .= 'From: Reachup <no-reply@reachup.com>';
            // mail($to,$subject,$messagetemp, implode("\r\n", $headers));

            return redirect('reset_my_password/'.$a['token'])->with(
                ['token' => $a['token'], 'error'=>'', 'success'=>'Your password has been reset successfully!']
            );
        }  else {
            return redirect('reset_my_password/'.$a['token'])->with(
                ['token' => $a['token'], 'error'=>'This password reset token is invalid.', 'success'=>'']
            );
            
        }
        
    }



    public function confirmation_email_send(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required',
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors()->first());       
            }
            // $input = $request->all();
            $user = User::where('email', $request->email)->where(function($q){
                $q->where('user_type_id',1)->orWhere('user_type_id',2);
            })->first();
            // dd($user);
            if(!$user){
                $admin = User::where('email', $request->email)->where('user_type_id', 3)->first();
                if($admin)
                    return $this->sendError('This account can’t be used for app since this has been associated with admin dashboard');
                return $this->sendError('Email not found');
            } else {
                if(!$user->email_verified_at){
                    // $emailTemplate = view('emails.confirmation_email',[ 'user' => $user]);
                    // $messagetemp = $emailTemplate->render();
                    
                    // $to = $request->email;
                    // $subject = "Confirmation Email";
                    // // Always set content-type when sending HTML email
                    // $headers[] = "MIME-Version: 1.0";
                    // $headers[] .= "Content-type:text/html;charset=UTF-8";

                    // // More headers
                    // $headers[] .= 'From: Reachup <no-reply@reachup.com>';
                    // mail($to,$subject,$messagetemp, implode("\r\n", $headers));

                    Mail::send('emails.confirmation_email', ['user' => $user], function ($m) use ($user) {
                        //$m->from('no_reply@botanicalgarden.patronassist.com', 'Museum');
                        $m->to($user->email, $user->name)->subject('Confirmation Email');
                    });
                    return $this->sendResponse([], 'Email has been sent successfully.');
                    
                } else {
                    return $this->sendResponse([], 'Email already confirmed.');
                }
                
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

    public function confirmation_request($email)
    {
        // dd(Crypt::decrypt($email));
        if(isset($email)){
            $user_email = Crypt::decrypt($email);

            $user = User::where('email', $user_email)->first();
            if($user->email_verified_at == null){

                if($user){

                    $success = $this->email_confirmation($user_email);
                    if($success ){
                        return view('auth.email_confirmation_response')->with('success', 'Email is verified. you may login with your account now');
                    } else {
                        return view('auth.email_confirmation_response')->with('error','Something went wrong or Token mismatch with your given email address');
        
                    }
                } else {
                    return view('auth.email_confirmation_response')->with('error','User not exist');
                }
            } else {
                return view('auth.email_confirmation_response')->with('success','Email already verified');

            }

            
        }
    }


    // public function email_confirmation(Request $request)
    // {
    //     // dd($request->all());
    //     // dd(Crypt::decrypt($request->email));
    //     if(isset($request->email)){
    //         if($request->token != $request->email){
    //             return back()->with(['error'=>'Token mismatch with your given email address']);
    //         } 
    //         $user_email = $request->email;
    //         $user = User::where('email', $user_email)->first();
    //         // dd($user);
    //         $user->update(['email_verified_at'=> now()]);
    //         return back()->with(['success'=>'Email is verified. you may login with your account now']);
    //     } else {
    //         return back()->with(['error'=>'Email address is missing']);
    //     }
    // }

    public function email_confirmation($email)
    {
        // dd($email);
        if($email){
            
            $user_email = $email;
            $user = User::where('email', $user_email)->update(['email_verified_at'=> now()]);
            // dd($user);
            // $user->update(['email_verified_at'=> now()]);
            return true;
        } else {
            return false;
        }
    }

    public function confirmation_email_view()
    {
        $user = Auth::user();
        $token = Str::random(60);
        return view('emails.password_changed', compact('user', 'token'));
    }
    
    
    public function delete_account()
    {
        try {
            $user = Auth::user();
            // dd($user);
            $user->delete();
            return $this->sendResponse([], 'Account has been deleted successfully.');
        } catch (\Throwable $th) {
            //throw $th;
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }
        }
        
    }
    

}
