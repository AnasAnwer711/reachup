<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommentsToPaypalTransactionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('paypal_transaction_details', function (Blueprint $table) {
            $table->text('comments')->after('pay_amount')->nullable();
            $table->text('file')->after('comments')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('paypal_transaction_details', function (Blueprint $table) {
            $table->dropColumn(['comments', 'file']);
        });
    }
}
