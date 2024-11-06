<?php

namespace Modules\HRMS\app\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\HRMS\app\Events\ScriptStatusCreatedEvent;
use Modules\HRMS\app\Listeners\ScriptStatusCreatedListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ScriptStatusCreatedEvent::class => [
            ScriptStatusCreatedListener::class,
        ],
    ];
}
