<?php

namespace App\Http\Controllers;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use Illuminate\Http\Request;

class PaypalController extends Controller
{
    
    public function createPayment()
    {
        $api = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                'AebW0-wKTfbvVmZS2ZWsltJCBwASRgThUSBFF8_1AYhxA29dJ_wn4yrJ-8auSADchSOJPNt1E1a_0aS-', //clientid
                'EGbiyNFJlZxsQmS84o0fQALx10ywlZZqaefPhfpYwrSm72om5W5CNHJyT0KEttM6Umyf8I461ntMkETl'  //secretid
            )
        );


        // Create new payer and method
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        // // Set redirect urls
        // $redirectUrls = new RedirectUrls();
        // $redirectUrls->setReturnUrl("http://localhost:3000/process")
        // ->setCancelUrl("http://localhost:3000/cancel");

        // Set payment amount
        $amount = new Amount();
        $amount->setCurrency("USD")
        ->setTotal(4.54);

        // Set transaction object
        $transaction = new Transaction();
        $transaction->setAmount($amount)
        ->setDescription("Payment description");

        // Create the full payment object
        $payment = new Payment();
        $payment->setIntent("authorize")
        ->setPayer($payer)
        // ->setRedirectUrls($redirectUrls)
        ->setTransactions(array($transaction));
    }
}
