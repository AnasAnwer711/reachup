<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDefaultSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('default_settings', function (Blueprint $table) {
            $table->longText('client_id');
            $table->longText('secret_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('default_settings', function (Blueprint $table) {
            $table->dropColumn('client_id');
            $table->dropColumn('secret_id');
        });
    }
}
