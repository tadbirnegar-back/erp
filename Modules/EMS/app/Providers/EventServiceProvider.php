<?php

namespace Modules\EMS\app\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\EMS\app\Events\CreateMeetingEvent;
use Modules\EMS\app\Events\EnactmentMeetingEvent;
use Modules\EMS\app\Events\EnactmentStatusCreatedEvent;
use Modules\EMS\app\Events\RecruitmentStatusCreatedEvent;
use Modules\EMS\app\Listeners\CreateMeetingListener;
use Modules\EMS\app\Listeners\EnactmentMeetingListener;
use Modules\EMS\app\Listeners\RecruitmentStatusCreatedListener;
use Modules\EMS\app\Listeners\StoreEnactmentStatusInQueueListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        EnactmentStatusCreatedEvent::class => [
            StoreEnactmentStatusInQueueListener::class,
        ],

        CreateMeetingEvent::class => [
            CreateMeetingListener::class,
        ],

        EnactmentMeetingEvent::class => [
            EnactmentMeetingListener::class,
        ],

        RecruitmentStatusCreatedEvent::class => [
            RecruitmentStatusCreatedListener::class,
        ]
    ];
}
