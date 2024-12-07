<?php

namespace Modules\EMS\app\Listeners;

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
            $meetingDate = $event;


//            $englishJalaliDateString = \Morilog\Jalali\CalendarUtils::convertNumbers($meetingDate, true);
//            $gregorianDate = CalendarUtils::createCarbonFromFormat('Y/m/d H:i:s', $englishJalaliDateString);
//            $targetDate = Carbon::parse($gregorianDate);
//            $currentDate = Carbon::now();
//            $delayInSeconds = $targetDate->diffInSeconds($currentDate, false); // false for negative values
//            $delayInSeconds -= 86400;
//            if ($delayInSeconds > 0) {
//                dispatch(new StoreMeetingJob($event->meeting))->delay($delayInSeconds);
//            }
        }

    }
}
