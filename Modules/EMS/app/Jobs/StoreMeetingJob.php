<?php

namespace Modules\EMS\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Modules\AAA\app\Models\User;
use Modules\EMS\app\Http\Traits\DateTrait;
use Modules\EMS\app\Http\Traits\MeetingTrait;
use Modules\EMS\app\Models\Meeting;
use Modules\EMS\app\Notifications\MeetingLastDayNotifications;
use Modules\PersonMS\app\Models\Person;

class StoreMeetingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, DateTrait, MeetingTrait;

    /**
     * Create a new job instance.
     */

    protected $meeting;

    public function __construct(Meeting $meeting)
    {
        $this->meeting = $meeting;
        $meetingId = $meeting->id;
        $jobIds = DB::table('queueable_jobs')
            ->where('payload', 'like', '%StoreMeetingJob%')
            ->where('payload', 'like', "%i:$meetingId%")
            ->select('id')
            ->get();

        foreach ($jobIds as $jobId) {
            DB::table('queueable_jobs')
                ->where('id', $jobId->id)
                ->delete();
        }

    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $cancelStatus = $this->meetingCancelStatus();
        if ($this->meeting->latestStatus->id != $cancelStatus->id) {
            // Log the meeting date
            // Extract the month number from the Jalali date
            $jalaliDate = $this->meeting->meeting_date; // e.g., ۱۴۰۴/۰۸/۰۹
            $parts = explode('/', $jalaliDate); // Split the date string by '/'
            $monthNumber = $parts[1]; // Get the second part as the month number
            $day = $parts[2];

            //For Month
            $eng = $this->persianNumbersToEng($monthNumber);

            $monthName = $this->humanReadableDate($eng);


            //For Day
            $daywithoutZero = $this->removeLeftZero($day);

            //message text for date
            $messageTextDate = $daywithoutZero['day'] . ' ' . $monthName . ' ساعت ' . $daywithoutZero['time'];

//        Log::info($messageTextDate);


            // Process members
            $members = $this->meeting->load('meetingMembers'); // Load related 'meetingMembers'


            foreach ($members->meetingMembers as $member) {
                $user = User::find($member->employee_id);
                $username = Person::find($user->person_id)->display_name;

                $user->notify(new MeetingLastDayNotifications($username, $messageTextDate));
            }
        } else {
            $this->delete();
            return;
        }
    }

}
