<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCronNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cron_notifications', function (Blueprint $table) {
            $table->boolean('is_notified')->default(0);
            $table->datetime('notified_datetime')->nullable();
            $table->longText('notify_tokens')->nullable();
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
            $table->dropColumn(['is_notified', 'notified_datetime','notify_tokens']);
        });
    }
}
