<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToPaypalTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('paypal_transactions', function (Blueprint $table) {
            $table->double('org_advisor_fee');
            $table->integer('org_advisor_percentage');
            $table->double('org_amount');
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
            $table->dropColumn('org_advisor_fee');
            $table->dropColumn('org_advisor_percentage');
            $table->dropColumn('org_amount');
        });
    }
}
