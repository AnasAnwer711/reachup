<?php

namespace App\Http\Controllers;

use App\LoginSecurity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginSecurityController extends Controller
{
    public function authenticate(Request $request)
    {

        $user = Auth::user();
        // dd($user);
        if(isset($user->loginSecurity) && $user->loginSecurity->isValid){
            Auth::user()->loginSecurity->update(['isValid'=>false]);
            return redirect()->route('dashboard');
        }
        $google2fa_url = "";
        $secret_key = "";
            // dd($user->loginSecurity());
        if($user->loginSecurity()->exists()){
            $google2fa = (new \PragmaRX\Google2FAQRCode\Google2FA());
            $google2fa_url = $google2fa->getQRCodeInline(
                'Reachup',
                $user->email,
                $user->loginSecurity->google2fa_secret
            );
            $secret_key = $user->loginSecurity->google2fa_secret;
        }

        $data = array(
            'user' => $user,
            'secret' => $secret_key,
            'google2fa_url' => $google2fa_url
        );

        return view('auth.authenticate')->with('data', $data);

    }

    public function generate2faSecret(){
        $user = Auth::user();
        // dd($user);
        Session::put('newSecret', true);

        // Initialise the 2FA class
        $google2fa = (new \PragmaRX\Google2FAQRCode\Google2FA());


        LoginSecurity::updateOrCreate([
            'user_id'   => $user->id,
        ],[
            'google2fa_secret' => $google2fa->generateSecretKey(),
        ]);
        return redirect()->route('authenticate');
    }

   
}
