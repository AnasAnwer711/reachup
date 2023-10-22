<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaypalTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paypal_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('payment_id')->nullable()->comment = 'Can be use for order details i.e: v2/checkout/orders/{payment_id} - Must use for approval order i.e: https://www.sandbox.paypal.com/checkoutnow?token={payment_id} - Once approved, authorize from: v2/checkout/orders/{payment_id}/authorize';
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paypal_transactions');
    }
}
