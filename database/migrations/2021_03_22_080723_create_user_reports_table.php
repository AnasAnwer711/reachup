<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('target_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_reports');
    }
}
