<?php

namespace Modules\LMS\App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\LMS\app\Events\CourseAccessDateEvent;
use Modules\LMS\app\Events\CourseExpirationEvent;
use Modules\LMS\app\Events\PaymentCreatedEvent;
use Modules\LMS\app\Listeners\CourseAccessDateListener;
use Modules\LMS\app\Listeners\CourseExpirationListener;
use Modules\LMS\app\Listeners\PaymentCreatedListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        PaymentCreatedEvent::class => [
            PaymentCreatedListener::class,
        ],
        CourseExpirationEvent::class => [
            CourseExpirationListener::class,
        ],
        CourseAccessDateEvent::class => [
            CourseAccessDateListener::class,
        ]
    ];
}
