<?php

namespace Modules\EMS\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\AAA\app\Models\User;
use Modules\EMS\app\Http\Enums\MeetingTypeEnum;
use Modules\EMS\app\Http\Traits\DateTrait;
use Modules\EMS\app\Http\Traits\MeetingTrait;
use Modules\EMS\app\Models\Meeting;
use Modules\EMS\app\Notifications\MeetingLastDayNotifications;
use Modules\OUnitMS\app\Models\CityOfc;
use Modules\PersonMS\app\Models\Person;

class StoreMeetingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, DateTrait, MeetingTrait;

    /**
     * Create a new job instance.
     */

    protected $meetingID;

    public function __construct(int $meetingID)
    {
        $this->meetingID = $meetingID;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $meeting = Meeting::with('latestStatus', 'meetingMembers')->find($this->meetingID);
        $cancelStatus = $this->meetingCancelStatus();
        if ($meeting->latestStatus->id != $cancelStatus->id) {
            $meetingOunit = $meeting->load('ounit.ancestorsAndSelf', 'meetingType');
            $cityUnit = $meetingOunit->ounit->ancestorsAndSelf
                ->where('unitable_type', CityOfc::class)
                ->first();
            $bakhshdari = $meetingOunit -> ounit -> name;
            $meetingType = $meetingOunit->meetingType->title;
            $finalOunit = $bakhshdari.' '.$cityUnit->name;

            $MtAndOunitText = "$meetingType $finalOunit";



            $jalaliDate = $meeting->meeting_date;
            $parts = explode('/', $jalaliDate);
            $monthNumber = $parts[1];
            $day = $parts[2];

            $eng = $this->persianNumbersToEng($monthNumber);

            $monthName = $this->humanReadableDate($eng);


            $daywithoutZero = $this->removeLeftZero($day);

            $messageTextDate = $daywithoutZero['day'] . ' ' . $monthName . ' ساعت ' . $daywithoutZero['time'];



            $members = $meeting->load('meetingMembers');


            foreach ($members->meetingMembers as $member) {
                $user = User::find($member->employee_id);
                $username = Person::find($user->person_id)->display_name;

                $user->notify((new MeetingLastDayNotifications($username, $messageTextDate , $MtAndOunitText ))->onQueue('default'));
            }
        }


    }

}
