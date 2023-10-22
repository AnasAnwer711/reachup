<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToCronNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cron_notifications', function (Blueprint $table) {
            $table->enum('status', ['pending','process','reject', 'accept','cancel','completed'])->after('before')->nullable();
            $table->enum('after',['1 minute'])->after('datetime')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cron_notifications', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
