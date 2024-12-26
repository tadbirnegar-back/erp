<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;
use Laravel\Horizon\HorizonApplicationServiceProvider;


class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeMailNotificationsTo('example@example.com');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     */
    protected function gate(): void
    {
            Gate::define('viewHorizon', function ($user = null) {
                $allowedIps = [
                    '2.187.37.91',
                    '2.187.34.161',
                ];

                return in_array(Request::instance()->ip(), $allowedIps); // Correct usage
            });
    }
}
