<?php

namespace Modules\EMS\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\AAA\app\Models\User;
use Modules\EMS\app\Http\Traits\DateTrait;
use Modules\EMS\app\Models\Meeting;
use Modules\EMS\app\Notifications\MeetingLastDayNotifications;
use Modules\PersonMS\app\Models\Person;

class StoreMeetingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, DateTrait;

    /**
     * Create a new job instance.
     */

    protected $meeting;

    public function __construct(Meeting $meeting)
    {
        $this->meeting = $meeting;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Log the meeting date
        Log::info($this->meeting->meeting_date);

        // Extract the month number from the Jalali date
        $jalaliDate = $this->meeting->meeting_date; // e.g., ۱۴۰۴/۰۸/۰۹
        $parts = explode('/', $jalaliDate); // Split the date string by '/'
        $monthNumber = $parts[1]; // Get the second part as the month number
        $day = $parts[2];

        //For Month
        $eng = $this->persianNumbersToEng($monthNumber);

        $monthName = $this->humanReadableDate($eng);


        //For Day
        $daywithoutZero = $this->removeLeftZero($monthNumber);

        //message text for date
        $messageTextDate = "$daywithoutZero $monthName $parts[0]";

        Log::info($messageTextDate);


        // Process members
        $members = $this->meeting->load('meetingMembers'); // Load related 'meetingMembers'


        foreach ($members->meetingMembers as $member) {
            $user = User::find($member->employee_id);
            $username = Person::find($user->person_id)->display_name;

            $user->notify(new MeetingLastDayNotifications($username, $messageTextDate));
            Log::info("Username: $user");
        }
    }

}
