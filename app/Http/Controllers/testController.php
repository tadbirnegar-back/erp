<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use Modules\ACMS\app\Http\Trait\BudgetItemsTrait;
use Modules\ACMS\app\Http\Trait\BudgetTrait;
use Modules\ACMS\app\Http\Trait\CircularTrait;
use Modules\ACMS\app\Http\Trait\FiscalYearTrait;
use Modules\ACMS\app\Http\Trait\OunitFiscalYearTrait;
use Modules\BNK\app\Models\Bank;
use Modules\EMS\app\Jobs\PendingForHeyaatStatusJob;
use Modules\EMS\app\Jobs\StoreEnactmentStatusJob;
use Modules\EMS\app\Jobs\StoreEnactmentStatusKarshenasJob;
use Modules\EMS\app\Jobs\StoreMeetingJob;
use Modules\EMS\app\Models\Enactment;


class testController extends Controller
{
    use FiscalYearTrait, CircularTrait, OunitFiscalYearTrait, BudgetTrait, BudgetItemsTrait;

    public function run()
    {
        for ($i = 0; $i < 5; $i++) {
            $chequeData[] = [
                'segmentNumber' => 100 + $i,
            ];
        }
        dd($chequeData);
        dd(Bank::getTableName());
        $ens = [
            141, 138, 139, 140
            // 166,169,177,   //7 bahman 2 pm

            // 171,173,          // 7bahman 8am
            // 179,180,181,182,163,164,178,188,   //8 bahman
            // 185,192
            // 184
        ];
        $meetings = [];
        foreach ($ens as $en) {
            $enactment = Enactment::with("latestHeyaatMeeting")->find($en);

            // Ensure meeting_date is in Carbon instance (convert if necessary)
            $meetingDate1 = $enactment->latestHeyaatMeeting->getRawOriginal('meeting_date');
            $meetingDate2 = $enactment->latestHeyaatMeeting->getRawOriginal('meeting_date');
            $meetingDate3 = $enactment->latestHeyaatMeeting->getRawOriginal('meeting_date');


            $delayHeyat = Carbon::parse($meetingDate1)->addDays(1);
            $delayKarshenas = Carbon::parse($meetingDate2)->addDays(1);


            // Dispatch the job with the calculated delay
            // Convert the fetched date to a Carbon instance

            $delayPending = Carbon::parse($meetingDate3);
            $alertMembers = Carbon::parse($meetingDate3)->subDays(1);


            StoreEnactmentStatusJob::dispatch($en)->delay($delayHeyat);   // ray khodkar heyaat
            StoreEnactmentStatusKarshenasJob::dispatch($en)->delay($delayKarshenas);  // ray khdkar kharshenas
            PendingForHeyaatStatusJob::dispatch($en)->delay($delayPending);  // dar entezar barresi
            $meetings[] = $enactment->latestHeyaatMeeting;
        }

        $uniqueMeetings = collect($meetings)->unique('id');
        foreach ($uniqueMeetings as $meeting) {
            $meetingDate3 = $meeting->getRawOriginal('meeting_date');

            $alertMembers = Carbon::parse($meetingDate3)->subDays(1);

            StoreMeetingJob::dispatch($meeting->id)->delay($alertMembers);
        }

        $output = "<!DOCTYPE html>
    <html>
    <head>
        <title>Test Debugbar</title>
    </head>
    <body>
    </body></html>";


        echo $output;

    }
}
