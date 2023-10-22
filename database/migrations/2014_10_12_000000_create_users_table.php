<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_type_id')->nullable()->constrained()->onDelete('cascade');	
            $table->string('name')->nullable();
            $table->string('username')->unique()->nullable();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('phone')->nullable();
            $table->string('bio')->nullable();
            $table->longText('description')->nullable();
            $table->string('image')->nullable();
            $table->double('longitude', 8, 2)->nullable();
            $table->double('latitude', 8, 2)->nullable();
            $table->longText('address')->nullable();
            $table->enum('status', ['active', 'blocked'])->default('active');
            $table->boolean('is_superadmin')->default(0);
            $table->rememberToken();
            $table->timestamps();
            
        });
        DB::table('users')->insert([
            [
                'user_type_id' => 3,
                'name' => 'Charaf Reachup',
                'username' => 'charaf',
                'email' => 'charaf@reachup.us',
                'password' => bcrypt('password12'),
                'is_superadmin' => 1,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
