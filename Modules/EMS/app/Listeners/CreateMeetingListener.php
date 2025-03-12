<?php

namespace Modules\EMS\app\Listeners;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\AAA\app\Models\User;
use Modules\EMS\app\Http\Enums\MeetingTypeEnum;
use Modules\EMS\app\Jobs\StoreMeetingJob;
use Modules\EMS\app\Notifications\MeetingLastDayNotifications;
use Modules\PersonMS\app\Models\Person;

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
        if ($event->meeting->meetingType->title == "جلسه هیئت تطبیق" || $event->meeting->meetingType->title == MeetingTypeEnum::FREE_ZONE->value) {
            $meetingDate3 = convertDateTimeHaveDashJalaliPersianCharactersToGregorian($event->meeting->meeting_date);

            $alertDate = Carbon::parse($meetingDate3)->subDays(1);

            StoreMeetingJob::dispatch($event->meeting->id)->delay($alertDate);
            StoreMeetingJob::dispatch($event->meeting->id); // For Notifiying Users now
        }

    }
}
