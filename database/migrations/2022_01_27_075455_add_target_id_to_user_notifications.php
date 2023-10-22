<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTargetIdToUserNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_notifications', function (Blueprint $table) {
            $table->foreignId('target_id')->nullable()->after('user_reachup_id')->constrained('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_notifications', function (Blueprint $table) {
            $table->dropForeign(['target_id']);
            $table->dropColumn('target_id');
        });
    }
}
