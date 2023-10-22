<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register', 'API\RegisterController@register');
Route::post('login', 'API\RegisterController@login');
Route::post('forgot_password', 'API\RegisterController@forgot_password');
Route::post('confirmation_email_send', 'API\RegisterController@confirmation_email_send');

Route::post('social_login', 'API\RegisterController@social_login');
Route::get('user_types', 'API\CommonController@user_types');
Route::get('metadata', 'API\CommonController@metadata');

Route::middleware('auth:api')->group( function () {
    Route::resource('categories', 'API\CategoryController');
    Route::resource('reportings', 'API\ReportController');
    Route::resource('user_reportings', 'API\UserReportController');
    Route::get('sub_category/{category}', 'API\CategoryController@sub_category');
    Route::get('category_keywords/{category}', 'API\CategoryController@category_keywords');
    Route::get('explore', 'API\CommonController@explore');
    Route::get('find_advisor', 'API\CommonController@find_advisor');
    Route::get('suggest_advisor', 'API\CommonController@suggest_advisor');
    Route::get('explore/{category_id}', 'API\CommonController@explore_with_category');
    Route::get('fellow_details', 'API\CommonController@fellow_details');
    Route::resource('user_keywords', 'API\UserKeywordController');
    Route::resource('user_interests', 'API\UserInterestController');
    Route::resource('user_blocks', 'API\UserBlockController');
    Route::resource('user_notifications', 'API\UserNotificationController');
    Route::resource('user_follows', 'API\UserFollowController');
    Route::resource('user_ratings', 'API\UserRatingController');
    Route::resource('user_reachups', 'API\UserReachupController');
    Route::post('reachup_update', 'API\ReachupPaymentController@reachup_update');
    //payment
    // Route::post('reachup_payments', 'API\ReachupPaymentController@store');
    Route::get('show_order_detail/{reachup_id}', 'API\ReachupPaymentController@show_order_detail');
    Route::post('authorize_payment', 'API\ReachupPaymentController@authorize_payment');
    Route::post('void', 'API\ReachupPaymentController@void');
    Route::post('capture', 'API\ReachupPaymentController@capture');
    Route::post('cancel', 'API\ReachupPaymentController@cancel');
    Route::post('payout_item', 'API\ReachupPaymentController@payout_item');
    //payment
    Route::resource('advisor_details', 'API\AdvisorDetailController');
    Route::resource('payment_details', 'API\PaymentDetailController');
    
    Route::post('logout', 'API\RegisterController@logout');
    Route::post('delete_account', 'API\RegisterController@delete_account');
    Route::get('profile', 'API\RegisterController@profile_index');
    Route::get('profile_stats', 'API\RegisterController@profile_stats');
    Route::post('profile', 'API\RegisterController@profile_store');
    Route::post('update_image', 'API\RegisterController@update_image');
    Route::post('update_paypal_email', 'API\RegisterController@update_paypal_email');
    Route::post('update_social_token', 'API\RegisterController@update_social_token');
    Route::get('social_token/{user_id}', 'API\RegisterController@social_token');
    
    Route::post('apply_coupon', 'API\CouponController@apply_coupon');
    Route::get('coupon_initialize', 'API\CouponController@coupon_initialize');
    Route::resource('coupon','API\CouponController');

    //Paypal
    // Route::post('reachup_payments', 'API\ReachupPaymentController@createPayment');
    // Route::post('create_order', 'API\ReachupPaymentController@executePayment');
});