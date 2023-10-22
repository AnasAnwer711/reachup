<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToTransactionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->double('user_fee',10,2)->after('status')->nullable();
            $table->integer('user_percentage')->after('user_fee')->nullable();
            $table->double('advisor_fee',10,2)->after('user_percentage')->nullable();
            $table->integer('advisor_percentage')->after('advisor_fee')->nullable();
            $table->double('reachup_fee',10,2)->after('advisor_percentage')->nullable();
            $table->integer('reachup_percentage')->after('reachup_fee')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_details', function (Blueprint $table) {
            //
        });
    }
}
