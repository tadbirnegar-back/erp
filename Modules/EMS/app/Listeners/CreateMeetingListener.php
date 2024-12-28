<?php

namespace Modules\EMS\app\Listeners;

use Carbon\Carbon;
use Modules\EMS\app\Jobs\StoreMeetingJob;

class CreateMeetingListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        if ($event->meeting->meetingType->title == "جلسه هیئت تطبیق") {
            $meetingDate3 = $event->meeting->getRawOriginal('meeting_date');

            $alertMembers = Carbon::parse($meetingDate3)->subDays(1);

            StoreMeetingJob::dispatch($event->meeting->id)->delay($alertMembers);
        }

    }
}
