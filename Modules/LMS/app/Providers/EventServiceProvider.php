<?php

namespace Modules\LMS\App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\LMS\app\Events\OnlineCreatedEvent;
use Modules\LMS\app\Events\PaymentCreatedEvent;
use Modules\LMS\app\Listeners\OnlineCreatedListener;
use Modules\LMS\app\Listeners\PaymentCreatedListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OnlineCreatedEvent::class => [
            OnlineCreatedListener::class,
        ],
        PaymentCreatedEvent::class => [
            PaymentCreatedListener::class,
        ]
    ];
}
