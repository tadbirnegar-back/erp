<?php

namespace Modules\EMS\app\Listeners;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\EMS\app\Events\EnactmentStatusCreatedEvent;
use Modules\EMS\app\Http\Traits\EMSSettingTrait;
use Modules\EMS\app\Http\Traits\EnactmentTrait;
use Modules\EMS\app\Jobs\StoreEnactmentStatusJob;
use Modules\EMS\app\Jobs\StoreEnactmentStatusKarshenasJob;
use Modules\EMS\app\Jobs\StoreEnactmentStatusKarshenasJobJob;
use Modules\EMS\app\Models\Enactment;
use Modules\EMS\app\Models\Meeting;

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

        if ($this->enactmentHeyaatStatus()->id == $enactmentStatus->status_id) {
            $enactment = Enactment::with("latestMeeting")->find($enactmentStatus->enactment_id);

            Log::info($enactment);

            // Ensure meeting_date is in Carbon instance (convert if necessary)
            $meetingDate = $enactment->latestMeeting->getRawOriginal('meeting_date');
            /**
             * @var Meeting $meetingDate
             */


//            $meetingDate = DB::table('meetings')
//                ->where('id', $meeting->id)
//                ->value('meeting_date');


            // Convert the fetched date to a Carbon instance
            $meetingDate = Carbon::parse($meetingDate);
            $meetingDate2 = Carbon::parse($meetingDate);

            $consultingDelay = $this->getConsultingAutoMoghayerat()?->value ?? 7;
            $HeyaatDelay = $this->getBoardAutoMoghayerat()?->value ?? 15;


            // Add 16 days and 5 minutes to the meeting date
            $delayHeyat = $meetingDate->addDays($HeyaatDelay + 1)->addMinutes(5);
            $delayKarshenas = $meetingDate2->addDays($consultingDelay + 1)->addMinutes(5);


            // Dispatch the job with the calculated delay
            StoreEnactmentStatusJob::dispatch($enactmentStatus->enactment_id)->delay($delayHeyat);
            StoreEnactmentStatusKarshenasJob::dispatch($enactmentStatus->enactment_id)->delay($delayKarshenas);
        }
    }


}
