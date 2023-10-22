<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvisorAvailabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advisor_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advisor_id')->constrained('advisor_details')->onDelete('cascade');
            $table->time('from_time',0);
            $table->time('to_time',0);
            $table->string('day');
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
        Schema::dropIfExists('advisor_availabilities');
    }
}
