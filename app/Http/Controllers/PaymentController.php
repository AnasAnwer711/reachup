<?php

namespace App\Http\Controllers;

use App\PaypalTransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function index()
    {
        $paypal_transaction_details = PaypalTransactionDetail::all();
        return view('payment.index', compact('paypal_transaction_details'));
    }

    public function edit($id)
    {
        $paypal_transaction_detail = PaypalTransactionDetail::find($id);
        return view('payment.edit', compact('paypal_transaction_detail'));
    }
    public function update(Request $request, $id)
    {
        try {
        // dd($request->all());
            $input = $request->all();
                
            $validator = Validator::make($input, [
                'comments' => 'required',
            ]);
            
            if($validator->fails()){
                return back()->withErrors($validator);      
            }
            $paypal_transaction_detail = PaypalTransactionDetail::findOrFail($id);
            $file = null;
            if($request->hasFile('image')){ 
                $image = $request->file('image');
                $name = time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('/payment_attachments');
                // dd(public_path);
        
                $image->move($destinationPath, $name);
                $file = url('/').'/payment_attachments/'.$name;
            }
            $paypal_transaction_detail->update([
                'comments' => $request->comments,
                'file' => $file,
                'status' => 'paid',
            ]);
            return redirect()->route('payment.index')->with('success', 'Payment Updated Successfully');
           
        } catch (\Throwable $th) {
            // dd($th);
            return redirect()->route('payment.index')->with('error', 'Error Occured - '.$th->getMessage());
        }
    }
}
