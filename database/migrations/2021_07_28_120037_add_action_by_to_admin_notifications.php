<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActionByToAdminNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_notifications', function (Blueprint $table) {
            $table->foreignId('action_by')->nullable()->after('target_id')->constrained('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admin_notifications', function (Blueprint $table) {
            $table->dropForeign(['action_by']);
            $table->dropColumn('action_by');
        });
    }
}
