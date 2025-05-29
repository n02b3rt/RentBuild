<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\VerifyEmailViewResponse;
use App\Http\Responses\VerifyEmailViewResponse as CustomVerifyEmailViewResponse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */


    public function register()
    {
        $this->app->singleton(VerifyEmailViewResponse::class, CustomVerifyEmailViewResponse::class);
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
