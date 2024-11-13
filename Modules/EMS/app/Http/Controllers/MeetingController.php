<?php

namespace Modules\EMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\AAA\app\Models\User;
use Modules\EMS\app\Http\Requests\UpdateMeetingDateReq;
use Modules\EMS\app\Models\Meeting;
use Modules\EMS\app\Models\MeetingMember;

class MeetingController extends Controller
{
    public function changeMeetingDate(UpdateMeetingDateReq $req, $id)
    {
        //$user = Auth::user();
        $user = User::find(2086);
        $meeting = Meeting::find($id);

        if (!$meeting || $user->id != $meeting->creator_id) {
            return response()->json([
                "situation" => false
            ], 403);
        }

        try {
            // Normalize the separator from `-` to `/`
            $normalizedDate = str_replace('-', '/', $req->newDate);

            // Convert Persian numbers to English
            $englishJalaliDateString = \Morilog\Jalali\CalendarUtils::convertNumbers($normalizedDate, true);

            // Convert Jalali to Gregorian and create Carbon instance
            $dateTimeString = \Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d', $englishJalaliDateString)
                ->toDateTimeString();

            $meeting->meeting_date = $normalizedDate;

            $meeting->save();
            return response()->json([
                'situation' => true,
                'dateTime' => $meeting->meeting_date,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                "situation" => false,
                "error" => "Invalid date format or conversion failed.",
                "details" => $e->getMessage()
            ], 400);
        }
    }


    public function store()
    {
        $meeting = Meeting::create([
            'creator_id' => 2086,
            'isTemplate' => true,
            'meeting_type_id' => 3,
            'ounit_id' => 3864
        ]);

        MeetingMember::create([
            'meeting_id' => $meeting->id,
            'mr_id' => 5,
            'employee_id' => 2126
        ]);


        MeetingMember::create([
            'meeting_id' => $meeting->id,
            'mr_id' => 2,
            'employee_id' => 2126
        ]);
    }


}
