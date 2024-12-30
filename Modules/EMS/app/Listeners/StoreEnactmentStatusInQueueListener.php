<?php

namespace Modules\EMS\app\Listeners;

use Carbon\Carbon;
use Modules\EMS\app\Events\EnactmentStatusCreatedEvent;
use Modules\EMS\app\Http\Traits\EMSSettingTrait;
use Modules\EMS\app\Http\Traits\EnactmentTrait;
use Modules\EMS\app\Jobs\PendingForHeyaatStatusJob;
use Modules\EMS\app\Jobs\StoreEnactmentStatusJob;
use Modules\EMS\app\Jobs\StoreEnactmentStatusKarshenasJob;
use Modules\EMS\app\Jobs\StoreEnactmentStatusKarshenasJobJob;
use Modules\EMS\app\Models\Enactment;

class StoreEnactmentStatusInQueueListener
{
    use EnactmentTrait, EMSSettingTrait;


    /**
     * Create the event listener.
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     */
    public function handle(EnactmentStatusCreatedEvent $event): void
    {
        // Retrieve the status from the EnactmentStatus model
        $enactmentStatus = $event->encStatus;

        if ($this->enactmentPendingForHeyaatDateStatus()->id == $enactmentStatus->status_id) {
//            $timeNow = Carbon::now();


            $enactment = Enactment::with("latestHeyaatMeeting")->find($enactmentStatus->enactment_id);

            // Ensure meeting_date is in Carbon instance (convert if necessary)
            $meetingDate1 = $enactment->latestHeyaatMeeting->getRawOriginal('meeting_date');
            $meetingDate2 = $enactment->latestHeyaatMeeting->getRawOriginal('meeting_date');
            $meetingDate3 = $enactment->latestHeyaatMeeting->getRawOriginal('meeting_date');

//            $receiptMaxDay = $this->getReceptionMaxDays()?->value ?? 7;


            // Add 16 days and 5 minutes to the meeting date
//            $delayHeyat = now()->addDays($receiptMaxDay + 1)->addMinutes(5);
//            $delayKarshenas = now()->addDays($receiptMaxDay + 1)->addMinutes(5);

            $delayHeyat = Carbon::parse($meetingDate1)->addDays(1);
            $delayKarshenas = Carbon::parse($meetingDate2)->addDays(1);


//            $alertHeayaatDelay = $timeNow->addDays($receiptMaxDay - 1)->addMinutes(5);
//            $alertKarshenasDelay = $timeNow->addDays($receiptMaxDay - 1)->addMinutes(5);


            // Dispatch the job with the calculated delay

            StoreEnactmentStatusJob::dispatch($enactmentStatus->enactment_id)->delay($delayHeyat);
            StoreEnactmentStatusKarshenasJob::dispatch($enactmentStatus->enactment_id)->delay($delayKarshenas);
            //AlertKarshenas::dispatch($enactmentStatus->enactment_id)->delay($alertKarshenasDelay);
            //AlertHeyaat::dispatch($enactmentStatus->enactment_id)->delay($alertHeayaatDelay);

            // Convert the fetched date to a Carbon instance
            $delayPending = Carbon::parse($meetingDate3);

            PendingForHeyaatStatusJob::dispatch($enactmentStatus->enactment_id)->delay($delayPending);

//            $alertMembers = Carbon::parse($meetingDate3)->subDays(1);

//            StoreMeetingJob::dispatch($enactment->latestHeyaatMeeting)->delay($alertMembers);

        }
    }


}
