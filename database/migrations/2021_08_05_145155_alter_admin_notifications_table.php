<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAdminNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_notifications', function (Blueprint $table) {
            $table->string('source_name')->after('source_id');
            $table->string('target_name')->after('target_id');
            $table->string('action_by_name')->after('action_by');
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
            $table->dropColumn('source_name');
            $table->dropColumn('target_name');
            $table->dropColumn('action_by_name');
        });
    }
}
