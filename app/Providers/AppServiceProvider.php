<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Schema\Builder; 
use App\Observers\UserObserver;
use App\Observers\ContractorObserver;
use App\Observers\NichesObserver;
use App\User;
use App\Contractor;
use App\Niche;

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
        Builder::defaultStringLength(191);
        // User::observe(UserObserver::class);
        Contractor::observe(ContractorObserver::class);
        Niche::observe(NichesObserver::class);
    }
}
