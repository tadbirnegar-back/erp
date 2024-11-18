<?php

namespace Modules\EMS\app\Listeners;

use Illuminate\Support\Facades\DB;

class EnactmentMeetingListener
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

        $encMeeting = $event->encMeeting;
        $encid = $encMeeting->enactment_id;
        $jobIds = DB::table('queueable_jobs')
            ->where('payload', 'like', '%StoreEnactmentStatusJob%')
            ->where('payload', 'like', "%i:$encid%")
            ->select('id')
            ->get();

        foreach ($jobIds as $jobId) {
            DB::table('queueable_jobs')
                ->where('id', $jobId->id)
                ->delete();
        }


        $jobIds = DB::table('queueable_jobs')
            ->where('payload', 'like', '%StoreEnactmentStatusKarshenasJob%')
            ->where('payload', 'like', "%i:$encid%")
            ->select('id')
            ->get();

        foreach ($jobIds as $jobId) {
            DB::table('queueable_jobs')
                ->where('id', $jobId->id)
                ->delete();
        }

    }
}
