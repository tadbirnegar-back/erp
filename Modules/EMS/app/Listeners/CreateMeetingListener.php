<?php

namespace Modules\EMS\app\Listeners;

use Carbon\Carbon;
use Modules\EMS\app\Jobs\StoreMeetingJob;
use Morilog\Jalali\CalendarUtils;

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
        $meetingDate = $event->meeting->meeting_date;

        $englishJalaliDateString = \Morilog\Jalali\CalendarUtils::convertNumbers($meetingDate, true);
        $gregorianDate = CalendarUtils::createCarbonFromFormat('Y/m/d', $englishJalaliDateString);
        $targetDate = Carbon::parse($gregorianDate);
        \Log::info($gregorianDate);
        $currentDate = Carbon::now();
        $delayInSeconds = $targetDate->diffInSeconds($currentDate, false); // false for negative values

        if ($delayInSeconds > 0) {
            dispatch(new StoreMeetingJob($event->meeting))->delay($meetingDate);
        }
    }
}
