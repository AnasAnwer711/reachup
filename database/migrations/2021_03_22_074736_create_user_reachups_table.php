<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserReachupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_reachups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('advisor_id')->constrained('users')->onDelete('cascade');
            $table->time('from_time',0);
            $table->time('to_time',0);
            $table->date('date');
            $table->string('reachup_subject');
            $table->enum('status', ['pending','process','reject', 'accept','cancel','completed']);
            $table->double('charges',8,2);
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
        Schema::dropIfExists('user_reachups');
    }
}
