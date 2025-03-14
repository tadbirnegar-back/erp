<?php
namespace Modules\EVAL\app\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\EVAL\app\Events\CircularExpirationEvent;
use Modules\EVAL\app\Listeners\CircularExpirationListener;


class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        CircularExpirationEvent::class => [
            CircularExpirationListener::class,
        ],

    ];
}
