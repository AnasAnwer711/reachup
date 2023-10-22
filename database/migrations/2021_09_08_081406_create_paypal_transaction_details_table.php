<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaypalTransactionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paypal_transaction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paypal_transaction_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['user', 'advisor']);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['paid', 'unpaid']);
            $table->double('pay_amount', 10,2);
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
        Schema::dropIfExists('paypal_transaction_details');
    }
}
