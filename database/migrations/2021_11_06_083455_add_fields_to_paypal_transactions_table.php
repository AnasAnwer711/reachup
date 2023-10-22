<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToPaypalTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('paypal_transactions', function (Blueprint $table) {
            $table->double('user_fee',10,2)->after('amount')->nullable();
            $table->integer('user_percentage')->after('user_fee')->nullable();
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
