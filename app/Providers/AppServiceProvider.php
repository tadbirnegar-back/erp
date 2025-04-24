<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Octane\Events\WorkerStarting;
use Laravel\Passport\Passport;
use Storage;
use URL;

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
        Passport::tokensExpireIn(now()->addDays(1));
        Passport::refreshTokensExpireIn(now()->addDays(7));

        Storage::disk('private')->buildTemporaryUrlsUsing(function ($path, $expiration, $options) {
            // Assuming the path is something like "2024/8/12/Cat03.jpg"
            $segments = explode('/', $path);

            // Ensure the path has the correct number of segments
            [$year, $month, $day, $filename] = $segments;

            return URL::temporarySignedRoute(
                'api.file.temp',
                $expiration,
                array_merge($options, [
                    'year' => $year,
                    'month' => $month,
                    'day' => $day,
                    'filename' => $filename,
                ])
            );
        });

        // Only when running under Octane
        if (isset($_SERVER['LARAVEL_OCTANE']) && ((int)$_SERVER['LARAVEL_OCTANE'] === 1)) {
            Event::listen(WorkerStarting::class, function (WorkerStarting $event) {
                Passport::loadKeysFrom(storage_path(''));
            });
        }
    }
}
