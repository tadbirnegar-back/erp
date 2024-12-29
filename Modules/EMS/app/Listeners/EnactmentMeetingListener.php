<?php

namespace Modules\EMS\app\Listeners;

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
//        $encMeeting = $event->encMeeting;
//        $encid = $encMeeting->enactment_id;
//
//        $jobTypes = [
//            'StoreEnactmentStatusJob',
//            'StoreEnactmentStatusKarshenasJob'
//        ];
//
//        DB::table('queueable_jobs')
//            ->where(function ($query) use ($jobTypes, $encid) {
//                foreach ($jobTypes as $jobType) {
//                    $query->orWhere('payload', 'like', "%$jobType%")
//                        ->where('payload', 'like', "%i:$encid%");
//                }
//            })
//            ->delete();
    }

}
