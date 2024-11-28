<?php

namespace Modules\EMS\app\Listeners;

use Carbon\Carbon;
use Modules\EMS\app\Events\EnactmentStatusCreatedEvent;
use Modules\EMS\app\Http\Traits\EMSSettingTrait;
use Modules\EMS\app\Http\Traits\EnactmentTrait;
use Modules\EMS\app\Jobs\AlertHeyaat;
use Modules\EMS\app\Jobs\AlertKarshenas;
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
            $timeNow = Carbon::now();

            $receiptMaxDay = $this->getReceptionMaxDays()?->value ?? 7;


            // Add 16 days and 5 minutes to the meeting date
            $delayHeyat = $timeNow->addDays($receiptMaxDay + 1)->addMinutes(5);
            $delayKarshenas = $timeNow->addDays($receiptMaxDay + 1)->addMinutes(5);


            $alertHeayaatDelay = $timeNow->addDays($receiptMaxDay - 1)->addMinutes(5);
            $alertKarshenasDelay = $timeNow->addDays($receiptMaxDay - 1)->addMinutes(5);


            // Dispatch the job with the calculated delay

            StoreEnactmentStatusJob::dispatch($enactmentStatus->enactment_id)->delay($delayHeyat);
            StoreEnactmentStatusKarshenasJob::dispatch($enactmentStatus->enactment_id)->delay($delayKarshenas);
            AlertKarshenas::dispatch($enactmentStatus->enactment_id)->delay($alertKarshenasDelay);
            AlertHeyaat::dispatch($enactmentStatus->enactment_id)->delay($alertHeayaatDelay);


            //Meeting Job

            $enactment = Enactment::with("latestMeeting")->find($enactmentStatus->enactment_id);

            // Ensure meeting_date is in Carbon instance (convert if necessary)
            $meetingDate = $enactment->latestMeeting->getRawOriginal('meeting_date');

            // Convert the fetched date to a Carbon instance
            $meetingDate = Carbon::parse($meetingDate);

            PendingForHeyaatStatusJob::dispatch($enactmentStatus->enactment_id)->delay($meetingDate);

        }
    }


}
