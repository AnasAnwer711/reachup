<?php

namespace App\Http\Middleware;
use App\Support\Google2FAAuthenticator;
use PragmaRX\Google2FA\Google2FA;


use Closure;
use Illuminate\Support\Facades\Auth;

class LoginSecurityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $google2fa = (new Google2FA());
        $secret = $request->input('secret');
        // dd($secret);
        if(isset($secret)){
            // dd('in2');
            $isValid = $google2fa->verifyKey(Auth::user()->loginSecurity->google2fa_secret, $secret, 2);
            if ($isValid) {
                // dd($next($request));
                $user = Auth::user();
                $user->loginSecurity->update(['isValid'=>true]);
                return $next($request);
            } else {
                return redirect()->route('authenticate')->with('error', 'One Time Password not authenticate');
            }
        } else {
            $authenticator = app(Google2FAAuthenticator::class)->boot($request);
            if($authenticator->isAuthenticated()){
                return $next($request);
            }
            // dd('in');
            return redirect()->route('authenticate');
        }
    }
}
