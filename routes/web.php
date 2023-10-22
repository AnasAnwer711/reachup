<?php

use App\LoginSecurity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Notifications\Slack;
use App\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // if(Auth::check()){
    //     if(isset(Auth::user()->loginSecurity) && Auth::user()->loginSecurity->isValid){
    //         return redirect()->route('dashboard');
    //     }
    //     return redirect()->route('authenticate');
    // } 
    return view('welcome');
});
// 2fa middleware
Route::post('2faVerify', function () {
    if(Session::has('newSecret')){
        Session::forget('newSecret');
    }
    return redirect('dashboard');
})->name('2faVerify')->middleware('2fa');

Route::get('authenticate','LoginSecurityController@authenticate')->name('authenticate')->middleware('auth');
Route::get('generate2faSecret','LoginSecurityController@generate2faSecret')->name('generate2faSecret')->middleware('auth');

Route::get('GrandeToto','LoginController@login')->name('login');

Route::post('GrandeToto','LoginController@doLogin')->name('doLogin');
Route::get('forgot_password','LoginController@forgot_password')->name('forgot_password');
Route::post('forgot_password','LoginController@email_forgot_password')->name('forgot_password');


//reset password for public users
Route::get('reset_my_password/{token}', 'API\RegisterController@reset_my_password')->name('password_reset');
Route::post('reset_password_request', 'API\RegisterController@reset_password_request');

Route::get('confirmation_email_view', 'API\RegisterController@confirmation_email_view');
//confirmation email for public users
Route::get('confirmation_request/{email}', 'API\RegisterController@confirmation_request');
Route::post('email_confirmation', 'API\RegisterController@email_confirmation');



Route::get('get_admin_notificaitons/{old_count?}', 'DashboardController@get_admin_notificaitons');


Route::group(['middleware' => ['auth', '2fa']], function () {
    // User needs to be authenticated to enter here.
    Route::get('dashboard','DashboardController@index')->name('dashboard');
    Route::get('dashboard/reachupchart','DashboardController@reachupchart')->name('reachupchart');
    Route::get('dashboard/earningchart','DashboardController@earningchart')->name('earningchart');
    Route::get('dashboard/reachupstatschart','DashboardController@reachupstatschart')->name('reachupstatschart');

    
    // Route::get('payment','PaymentController@index')->name('payment');
    Route::get('allUsers','UserController@allUsers')->name('allUsers');
    Route::post('notify_user/{id}','UserController@notify_user')->name('notify_user');
    Route::post('notify_multiple_users','UserController@notify_multiple_users')->name('notify_multiple_users');
    Route::post('temporary_images/{id}','UserController@temporary_images')->name('temporary_images');
    Route::get('edit_user/{id}','UserController@edit_user')->name('edit_user');
    Route::get('delete_user/{id}','UserController@destroy')->name('delete_user');
    Route::post('update_user_status/{id}','UserController@update_user_status')->name('update_user_status');
    Route::post('update_advisor_status/{id}','UserController@update_advisor_status')->name('update_advisor_status');
    Route::get('users','ReportController@users')->name('users');
    Route::get('professionals','ReportController@professionals')->name('professionals');
    Route::get('reachups','ReportController@reachups')->name('reachups');
    // Route::resource('admin','AdminController')->middleware(isSuperAdmin::class);
    Route::resource('administration', 'AdminController', [
        'names' => [
            'index' => 'admin.index',
            'create' => 'admin.create',
            'store' => 'admin.store',
            'show' => 'admin.show',
            'edit' => 'admin.edit',
            'update' => 'admin.update',
            'destroy' => 'admin.destroy',
            // etc...
        ]
    ])->middleware(isSuperAdmin::class);
    Route::post('default_percentage','DefaultSettingController@default_percentage')->name('default_percentage');
    Route::post('cancel_before_percentage','DefaultSettingController@cancel_before_percentage')->name('cancel_before_percentage');
    Route::post('cancel_after_percentage','DefaultSettingController@cancel_after_percentage')->name('cancel_after_percentage');
    Route::resource('setting','DefaultSettingController')->middleware(isSuperAdmin::class);
    Route::post('additional_charges/{id}','DefaultSettingController@additional_charges')->name('additional-charges')->middleware(isSuperAdmin::class);
    Route::resource('admin_notification','AdminNotificationController');
    Route::resource('payment','PaymentController');
    Route::resource('category','CategoryController');
    Route::get('get_categories','CategoryController@get_categories');
    Route::resource('reporting','ReportingController');
    // Route::resource('coupon','CouponController');
    Route::resource('profile','ProfileController');
    Route::put('update_password/{id}','ProfileController@update_password')->name('update_password');
});
Route::get('logout','LoginController@logout')->name('logout')->middleware('auth');
Route::get('template', function() {
    return view('a');
});

// ->middleware(['auth', '2fa']);


// Route::post('reachup_payments', 'API\ReachupPaymentController@store');
