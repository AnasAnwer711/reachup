<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateDefaultRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('default_rules', function (Blueprint $table) {
            $table->id();
            $table->enum('rule_type', ['default', 'cancel']);
            $table->enum('concern',['platform', 'advisor', 'user']);
            $table->integer('hour')->nullable();
            $table->enum('approximately',['before', 'after'])->nullable();
            $table->integer('percentage');
            $table->enum('action_by',['advisor', 'user']);
            $table->timestamps();
        });
        //when payment get successful from user
        //platform fees
        DB::table('default_rules')->insert(
            array(
                'rule_type' => 'default',
                'concern' => 'platform',
                'hour' => null,
                'approximately' => null,
                'percentage' => 10,
                'action_by' => 'user',
            )
        );
        //advisor fees
        DB::table('default_rules')->insert(
            array(
                'rule_type' => 'default',
                'concern' => 'advisor',
                'hour' => null,
                'approximately' => null,
                'percentage' => 90,
                'action_by' => 'user',
            )
        );

        //when user cancel before 48 hour of session start
        //user will get
        DB::table('default_rules')->insert(
            array(
                'rule_type' => 'cancel',
                'concern' => 'user',
                'hour' => 48,
                'approximately' => 'before',
                'percentage' => 95,
                'action_by' => 'user',
            )
        );
        //platform will get
        DB::table('default_rules')->insert(
            array(
                'rule_type' => 'cancel',
                'concern' => 'platform',
                'hour' => 48,
                'approximately' => 'before',
                'percentage' => 5,
                'action_by' => 'user',
            )
        );

        //when user cancel within 48 hour of session start
        //user will get
        DB::table('default_rules')->insert(
            array(
                'rule_type' => 'cancel',
                'concern' => 'user',
                'hour' => 48,
                'approximately' => 'after',
                'percentage' => 55,
                'action_by' => 'user',
            )
        );
        //platform will get
        DB::table('default_rules')->insert(
            array(
                'rule_type' => 'cancel',
                'concern' => 'platform',
                'hour' => 48,
                'approximately' => 'after',
                'percentage' => 5,
                'action_by' => 'user',
            )
        );
        //advisor will get
        DB::table('default_rules')->insert(
            array(
                'rule_type' => 'cancel',
                'concern' => 'advisor',
                'hour' => 48,
                'approximately' => 'after',
                'percentage' => 40,
                'action_by' => 'user',
            )
        );


        //when advisor cancel before 48 hour of session start
        //user will get
        DB::table('default_rules')->insert(
            array(
                'rule_type' => 'cancel',
                'concern' => 'user',
                'hour' => 48,
                'approximately' => 'before',
                'percentage' => 100,
                'action_by' => 'advisor',
            )
        );
        //advisor will get deduction
        DB::table('default_rules')->insert(
            array(
                'rule_type' => 'cancel',
                'concern' => 'advisor',
                'hour' => 48,
                'approximately' => 'before',
                'percentage' => -5,
                'action_by' => 'advisor',
            )
        );

        //when advisor cancel within 48 hour of session start
        //user will get
        DB::table('default_rules')->insert(
            array(
                'rule_type' => 'cancel',
                'concern' => 'user',
                'hour' => 48,
                'approximately' => 'after',
                'percentage' => 100,
                'action_by' => 'advisor',
            )
        );
        //advisor will get deduction
        DB::table('default_rules')->insert(
            array(
                'rule_type' => 'cancel',
                'concern' => 'advisor',
                'hour' => 48,
                'approximately' => 'after',
                'percentage' => -5,
                'action_by' => 'advisor',
            )
        );
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('default_rules');
    }
}
