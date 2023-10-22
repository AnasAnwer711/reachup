<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReachupCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reachup_coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_reachup_id')->constrained()->onDelete('cascade');
            $table->foreignId('coupon_id')->constrained();
            $table->string('code');
            $table->datetime('start')->nullable();
            $table->datetime('end')->nullable();
            $table->integer('percentage');
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
        Schema::dropIfExists('reachup_coupons');
    }
}
