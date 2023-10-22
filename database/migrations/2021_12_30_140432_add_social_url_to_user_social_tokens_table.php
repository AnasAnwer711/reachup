<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSocialUrlToUserSocialTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_social_tokens', function (Blueprint $table) {
            $table->text('social_url')->after('social_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_social_tokens', function (Blueprint $table) {
            $table->dropColumn('social_url');
        });
    }
}
