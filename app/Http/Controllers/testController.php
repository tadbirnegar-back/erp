<?php

namespace App\Http\Controllers;


use Modules\EMS\app\Models\Enactment;
use Modules\HRMS\app\Http\Traits\EmployeeTrait;

class testController extends Controller
{
    use EmployeeTrait;

    public function run()
    {
        $a = "[66,69]";
        $b = json_decode($a, true);

        $a = Enactment::first();
        dd($a->upshot);
        $b = $a->reviewStatuses;

        $c = $b->groupBy('id')
            ->map(function ($statusGroup) {
                return [
                    'status' => $statusGroup->first(),
                    'count' => $statusGroup->count()
                ];
            })
            ->sortByDesc('count')
            ->values();
        dd($c[0]['status']);
//        $x = Meeting::GetAllStatuses()->firstWhere('name', MeetingStatusEnum::APPROVED->value);
//        dd($x);
//        $dateString = \Morilog\Jalali\CalendarUtils::convertNumbers('۱۳۹۵/۰۲/۱۹', true); // 1395-02-19
//        $a = \Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d', $dateString)
//            ->toDateTimeString();//            ->format('Y/m/d H:i:s'); //2016-05-8
//        ;
//
//        dd($a);
//        $a = \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($a)); // 1395-02-19
////        dd($a);
//        $a = \Morilog\Jalali\CalendarUtils::convertNumbers($a); // ۱۳۹۵-۰۲-۱۹
//        dd($a);
////        $jDate = Jalalian::fromFormat('Y-m-d', '1403-12-01')->toDateTimeString();
//
//        dd($jDate);
    }

}

