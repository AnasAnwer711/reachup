<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServicePercentageColumnsToDefaultSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('default_settings', function (Blueprint $table) {
            $table->boolean('is_additional_charges')->default(0);
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->double('percentage')->nullable();
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
            $table->dropColumn(['is_additional_charges','title', 'description', 'percentage']);
        });
    }
}
