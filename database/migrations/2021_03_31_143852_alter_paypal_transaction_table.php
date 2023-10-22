<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPaypalTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('paypal_transactions', function (Blueprint $table) {
            $table->foreignId('reachup_id')->after('payment_id')->constrained('user_reachups')->onDelete('cascade');
            $table->string('card_no')->after('reachup_id')->nullable();
            $table->string('card_type')->after('reachup_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('paypal_transactions', function (Blueprint $table) {
            //
        });
    }
}
