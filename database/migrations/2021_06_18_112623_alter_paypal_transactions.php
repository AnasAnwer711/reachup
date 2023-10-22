<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPaypalTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('paypal_transactions', function (Blueprint $table) {
            $table->string('authorization_id')->nullable()->comment = 'Process to capture an order: v2/payments/authorizations/{authorization_id}/capture - For cancel/void order: v2/payments/authorizations/{authorization_id}/void';
            $table->string('transaction_id')->nullable()->comment = 'Use for refund: v2/payments/captures/{transaction_id}/refund - Use for payout item reference_id in body: v1/payments/referenced-payouts-items';
            $table->string('currency_code', 3)->nullable();
            $table->double('amount',10,2);
            $table->double('advisor_fee',10,2);
            $table->integer('advisor_percentage');
            $table->double('reachup_fee',10,2);
            $table->integer('reachup_percentage');
            $table->enum('state', ['intent', 'authorize', 'void', 'capture', 'cancel', 'complete']);
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
            $table->dropColumn('authorization_id');
            $table->dropColumn('transaction_id');
            $table->dropColumn('currency_code');
            $table->dropColumn('amount');
            $table->dropColumn('advisor_fee');
            $table->dropColumn('advisor_percentage');
            $table->dropColumn('reachup_fee');
            $table->dropColumn('reachup_percentage');
            $table->dropColumn('state');
        });
    }
}
