<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHandlingFeeColumnsToUserReachupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_reachups', function (Blueprint $table) {
            $table->string('handling_title')->after('charges')->nullable();
            $table->double('handling_percentage')->after('charges')->nullable();
            $table->double('handling_charges')->after('charges')->nullable();
            $table->double('paid_charges')->after('charges')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_reachups', function (Blueprint $table) {
            $table->dropColumn(['handling_title', 'handling_percentage', 'handling_charges', 'paid_charges']);
        });
    }
}
