<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserReachupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_reachups', function (Blueprint $table) {
            $table->string('token_id')->after('charges')->nullable();
            $table->double('session_duration')->after('charges')->nullable();
            $table->time('session_end_time')->after('charges')->nullable();
            $table->time('session_start_time')->after('charges')->nullable();
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
            //
        });
    }
}
