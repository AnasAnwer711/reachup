<?php

namespace App\Http\Controllers;

use App\LoginSecurity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    // use RegistersUsers {
    //     // change the name of the name of the trait's method in this class
    //     // so it does not clash with our own register method
    //        register as registration;
    //    }
    
    public function login()
    {
        if(Auth::check()){
            if(isset(Auth::user()->loginSecurity) && Auth::user()->loginSecurity->isValid){
                return redirect()->route('dashboard');
            }
            return redirect()->route('authenticate');
        }  
        return view('auth.login');
    }

    public function checkEmail($email)
    {
        $find1 = strpos($email, '@');
        $find2 = strpos($email, '.',$find1);
        return ($find1 !== false && $find2 !== false && $find2 > $find1);
    }
    
    public function doLogin(Request $request)
    {
        // dd($request->all());
        // validate the info, create rules for the inputs
        $rules = array(
            'user'    => 'required', // make sure the email is an actual email
            'password' => 'required|alphaNum|min:6' // password can only be alphanumeric and has to be greater than 3 characters
        );
        $remember = $request->has('remember_me') ? true : false;
        // run the validation rules on the inputs from the form
        $validator = Validator::make($request->all(), $rules,[
            'user.required' => 'The username or email field is required'
        ]);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return redirect()->route('login')->withErrors($validator) // send back all errors to the login form
                ->withInput($request->except('password')); // send back the input (not the password) so that we can repopulate the form
        } else {
            
            if ( $this->checkEmail($request->user) ) {
                $attempt = Auth::attempt(['email' => $request->user, 'password' => $request->password], $remember);
            } else {
                $attempt = Auth::attempt(['username' => $request->user, 'password' => $request->password], $remember);
            }
            // dd($attempt);
            // attempt to do the login
            if ($attempt) {
                // dd(Auth::user());
                if(Auth::user()->user_type_id != 3){
                    Auth::logout(); // log the user out of our application
                    return redirect()->route('login')->with('error', 'Unauthorize user');
                }
                $this->generate2faSecret(Auth::user());
                return redirect()->route('authenticate');
            } else {        
                // validation not successful, send back to form 
                return redirect()->route('login')->with('error', 'Unauthorize user');
            }

        }
    }

    protected function generate2faSecret($user){
        $user = Auth::user();
        // Initialise the 2FA class
        $google2fa = (new \PragmaRX\Google2FAQRCode\Google2FA());

        // Add the secret key to the registration data
        if(!$user->loginSecurity){
            Session::put('newSecret', true);

            LoginSecurity::create([
                'user_id'   => $user->id,
                'google2fa_secret' => $google2fa->generateSecretKey(),
            ]);
        }
    }

    public function logout()
    {
        if(Auth::user()->loginSecurity){
            Auth::user()->loginSecurity->update(['isValid'=>false]);
        }
        Auth::logout(); // log the user out of our application
        return redirect()->route('login');
    }

    public function forgot_password()
    {
        if(Auth::check()){
            return redirect()->route('dashboard');
        } 
        return view('auth.forgot-password');
    }

    public function email_forgot_password(Request $request)
    {
        $input = $request->all();

        $user = User::where('email', $request->email)->where('user_type_id',3)->first();
        // dd($user);
        if(!$user){
            return back()->with('error', 'User not found');
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
            // $subject = "Confirmation Email";
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

        return back()->with('success', 'Email has been sent successfully.');

        // dd($input);
    }
}
