<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\VerifyEmailViewResponse;
use App\Http\Responses\VerifyEmailViewResponse as CustomVerifyEmailViewResponse;
use PragmaRX\Google2FA\Google2FA;
use App\Services\TwoFactorService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        // istniejący singleton do niestandardowego widoku weryfikacji e-mail
        $this->app->singleton(
            VerifyEmailViewResponse::class,
            CustomVerifyEmailViewResponse::class
        );

        // nasz serwis 2FA
        $this->app->singleton(TwoFactorService::class, function($app) {
            return new TwoFactorService(
                $app->make(Google2FA::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

