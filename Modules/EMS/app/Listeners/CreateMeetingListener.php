<?php

namespace Modules\EMS\app\Listeners;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
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
            $meetingDate3 = convertDateTimeHaveDashJalaliPersianCharactersToGregorian($event->meeting->meeting_date);

            $alertDate = Carbon::parse($meetingDate3)->subDays(1);

            StoreMeetingJob::dispatch($event->meeting->id)->delay($alertDate);
        }

    }
}
