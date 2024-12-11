<?php

namespace Modules\EMS\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
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



        $jalaliDate = $this->meeting->meeting_date;
        $parts = explode('/', $jalaliDate);
        $monthNumber = $parts[1];
        $day = $parts[2];


        $eng = $this->persianNumbersToEng($monthNumber);

        $monthName = $this->humanReadableDate($eng);



        $daywithoutZero = $this->removeLeftZero($monthNumber);

        $messageTextDate = "$daywithoutZero $monthName $parts[0]";




        // Process members
        $members = $this->meeting->load('meetingMembers'); // Load related 'meetingMembers'

        foreach ($members->meetingMembers as $member) {
            $user = User::find($member->employee_id);
            $username = Person::find($user->person_id)->display_name;

            $user->notify(new MeetingLastDayNotifications($username, $messageTextDate));
        }
    }

}
