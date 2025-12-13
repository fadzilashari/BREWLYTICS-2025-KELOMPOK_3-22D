<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Activity::creating(function (Activity $activity) {
            if (Auth::check()) {
                $activity->causer_name = Auth::user()->name;
            }
        });
    }
}
