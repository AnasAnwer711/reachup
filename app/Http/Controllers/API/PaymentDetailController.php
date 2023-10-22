<?php

namespace App\Http\Controllers\API;

use App\PaymentDetail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class PaymentDetailController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'merchant_id' => 'required',
        ]);
            
            
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors()->first());       
        }

        $user_id = auth()->user()->id;

        User::where('id', $user_id)->update(['merchant_id' => $request->merchant_id,'is_payment_detail_completed' => 1]);

        return $this->sendResponse([], 'User payment details added successfully.');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PaymentDetail  $paymentDetail
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentDetail $paymentDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PaymentDetail  $paymentDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentDetail $paymentDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PaymentDetail  $paymentDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaymentDetail $paymentDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PaymentDetail  $paymentDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentDetail $paymentDetail)
    {
        //
    }
}
