<!DOCTYPE html>
<html>



<body style='-webkit-text-size-adjust: none; box-sizing: border-box; color: #74787E; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; height: 100%; line-height: 1.4; margin: 0; width: 100% !important;' bgcolor='#F2F4F6'>
    <table class='email-wrapper' width='100%' cellpadding='0' cellspacing='0' style='box-sizing: border-box; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; margin: 0; padding: 0; width: 100%;' bgcolor='#F2F4F6'><tr>
    <td align='center' style='box-sizing: border-box; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; word-break: break-word;'>
    <table class='email-content' width='100%' cellpadding='0' cellspacing='0' style='box-sizing: border-box; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; margin: 0; padding: 0; width: 100%;'><tr>
    <td class='email-body' width='100%' cellpadding='0' cellspacing='0' style='-premailer-cellpadding: 0; -premailer-cellspacing: 0; border-bottom-color: #EDEFF2; border-bottom-style: solid; border-bottom-width: 1px; border-top-color: #EDEFF2; border-top-style: solid; border-top-width: 1px; box-sizing: border-box; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; margin: 0; padding: 0; width: 100%; word-break: break-word;' bgcolor='#FFFFFF'>
    <table class='email-body_inner' align='center' width='570' cellpadding='0' cellspacing='0' style='box-sizing: border-box; font-family: Arial, Helvetica, sans-serif; margin: 0 auto; padding: 0; width: 570px;' bgcolor='#FFFFFF'><tr>
    <td class='content-cell' style='box-sizing: border-box; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; padding: 35px; word-break: break-word;'>
    <img alt="Reachup" style="margin:30px auto;width: 50%;display: block; background:#f39c12; padding: 10px;" src="https://reachup.us/public/dist/img/w-logo.png">
    <h1 style='box-sizing: border-box; color: #2F3133; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; font-size: 19px; font-weight: bold; margin-top: 0;' align='left'>Hi {{ $user->name ?? ''}},</h1>
    <p style='box-sizing: border-box; color: #74787E; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; font-size: 16px; line-height: 1.5em; margin-top: 0;' align='left'>Your acount has been registered, kindly proceed with the below link to activate your account. Thanks! </p>
    <table class='body-action' align='center' width='100%' cellpadding='0' cellspacing='0' style='box-sizing: border-box; font-family: Arial, Helvetica, sans-serif; margin: 30px auto; padding: 0; text-align: center; width: 100%;'><tr><td align='center' style='box-sizing: border-box; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; word-break: break-word;'>
    <table width='100%' border='0' cellspacing='0' cellpadding='0' style='box-sizing: border-box; font-family: Arial, Helvetica Neue, Helvetica, sans-serif;'><tr><td align='center' style='box-sizing: border-box; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; word-break: break-word;'>
    <table border='0' cellspacing='0' cellpadding='0' style='box-sizing: border-box; font-family: Arial, Helvetica Neue, Helvetica, sans-serif;'><tr><td style='box-sizing: border-box; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; word-break: break-word;'>
    <a href="{{url('confirmation_request', Crypt::encrypt($user->email ?? ''))}}" class='button button--green' target='_blank' style='-webkit-text-size-adjust: none; background: #f39c12; border-color: #f39c12; border-radius: 3px; border-style: solid; border-width: 10px 18px; box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16); box-sizing: border-box; color: #FFF; display: inline-block; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; text-decoration: none;'>Click here to verify</a></td></table></td></tr></table></td></tr></table>
    <p style='box-sizing: border-box; color: #74787E; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; font-size: 16px; line-height: 1.5em; margin-top: 0;' align='left'>Thanks,
    <br />The Reachup Team</p>
    <table class='body-sub' style='border-top-color: #EDEFF2; border-top-style: solid; border-top-width: 1px; box-sizing: border-box; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; margin-top: 25px; padding-top: 25px;'><tr><td style='box-sizing: border-box; font-family: Arial, Helvetica, sans-serif; word-break: break-word;'>
    <p class='sub' style='box-sizing: border-box; color: #74787E; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; font-size: 12px; line-height: 1.5em; margin-top: 0;' align='left'>If you’re having trouble with the button above, copy and paste the URL below into your web browser.</p>
    <p class='sub' style='box-sizing: border-box; color: #74787E; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; font-size: 12px; line-height: 1.5em; margin-top: 0;' align='left'>{{url('confirmation_request', Crypt::encrypt($user->email ?? ''))}}</p>
    </td></tr></table></td></tr></table></td></tr><tr><td style='box-sizing: border-box; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; word-break: break-word;'>
    <table class='email-footer' align='center' width='570' cellpadding='0' cellspacing='0' style='box-sizing: border-box; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; margin: 0 auto; padding: 0; text-align: center; width: 570px;'>
    <tr><td class='content-cell' align='center' style='box-sizing: border-box; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; padding: 35px; word-break: break-word;'>
    <p class='sub align-center' style='box-sizing: border-box; color: #AEAEAE; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; font-size: 12px; line-height: 1.5em; margin-top: 0;' align='center'>© 2021 Reachup. All rights reserved.</p>
    </td></tr></table></td></tr></table></td></tr></table>

</body>

</html>
