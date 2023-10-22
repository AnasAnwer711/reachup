<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('social_login_exist', function ($attribute, $value, $parameters, $validator) {
            $result = true;
            if (User::where('email', $value)->whereNull('user_type_id')->where('profile_complete', 0)->exists()) {
                $result = false;
            }
            return $result;
        });

        Schema::defaultStringLength(191);
    }
}
