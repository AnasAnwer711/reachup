<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reachup_id')->constrained('user_reachups')->onDelete('cascade');
            $table->foreignId('paypal_transaction_id')->constrained()->onDelete('cascade');
            $table->enum('action', ['intent', 'authorize', 'void', 'capture', 'cancel', 'complete']);
            $table->enum('status',['initiate', 'success', 'unsuccess']);
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
        Schema::dropIfExists('transaction_details');
    }
}
