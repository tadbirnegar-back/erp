<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Mockery\Exception;
use Modules\AAA\app\Http\Repositories\OtpRepository;
use Modules\AAA\app\Models\User;
use Modules\AAA\app\Notifications\OtpNotification;
use Modules\AAA\app\Notifications\TestNotification;
use Modules\AddressMS\app\Models\City;
use Modules\AddressMS\app\Models\District;
use Modules\AddressMS\app\Models\Town;
use Modules\AddressMS\app\Models\Village;
use Modules\HRMS\app\Models\Employee;
use Modules\OUnitMS\app\Notifications\VerifyInfoNotification;
use Tzsk\Sms\Exceptions\InvalidMessageException;
use Tzsk\Sms\Facades\Sms;

class testController extends Controller
{


    public function run()
    {

        $user = User::find(1);
        dd($user);
//        $user->load('person.personable.homeAddress', 'person.avatar', 'person.workForce.workforceable');
//        dd($user);
//        $workForce = $user->person->workForce;
//
//        if ($workForce->workforceable_type === Employee::class) {
//            $rs = $workForce->workforceable->recruitmentScripts;
//        }
//        $data = [
//            'info' => $user->person,
//            'recruitmentScripts' => $rs ?? null,
//
//        ];
//        $user->load('person.workForce.workForceable');
//        $workForce = $user->person->workForce->workForceable;
//        if ($workForce instanceof Employee) {
//            $rs = $workForce->recruitmentScripts;
//        }
//        $notif=$user->notifications()->where('type', '=', VerifyInfoNotification::class)->first();
//        if (is_null($notif)) {
//            $user->notify(new VerifyInfoNotification());
//            return $user->notifications()->where('type', '=', VerifyInfoNotification::class)->first();
//        } elseif (!$notif->read()) {
//            $notif->markAsRead();
//            dd( $notif);
//        }
//        $a = convertToDbFriendly('٦٦٦٦٦ ميزان تحقق بودجه مصوب دهياري');
//        dd($a);
//        $villages = json_decode(file_get_contents(realpath(__DIR__ . '/agh.json')), true);
////        $a = [];
//        $records = array_reduce($villages, function ($records, $village) {
//            $records[$village[0]][$village[1]][$village[2]][] = $village[3];
//            return $records;
//        }, []);
//        dd($records);

//        $districtsNotFound = [];
//        $townNotFound = [];
//        $villageNotFound = [];
//
//
//
//
//        foreach ($records as $key => $record) {
//            $city = City::where('name', $key)->first();
//            if (!is_null($city)) {
//                foreach ($record as $district => $towns) {
//                    $dist = District::where('name', $district)->where('city_id',$city->id)->first();
//
//                    if (is_null($dist)) {
//                        $districtsNotFound[] = $district;
//                    }else{
//                        foreach ($towns as $town => $villages) {
//                            $twn = Town::where('name', $town)->where('district_id', $dist->id)->first();
//                            if (is_null($twn)) {
//                                $townNotFound[$key][$district][] = $town;
//                            }else{
//                                foreach ($villages as $village) {
//                                    $vlg = Village::where('name', $village)->where('town_id', $twn->id)->first();
//                                    if (is_null($vlg)) {
//                                        $villageNotFound[$key][$district][$town][] = $village;
//
//                                    }
//                                }
//                            }
//                        }
//                    }
//
//
//                }
//            }
//        }
//        dd($districtsNotFound,$townNotFound,$villageNotFound);
//        foreach ($villages as $village) {
//
//            $a[$village[0]][$village[1]][$village[2]][] = $village[3];
//        }
//        dd($a);
//        dd($moduleCategoriesData);


//        $user = User::where('mobile', '9360390070')->first();
//
//        $a = OtpRepository::verify($user->id, 29199);
//        dd($a);


//        $baseUrl = url('/');
//
//        $response = Http::post("{$baseUrl}/oauth/token", [
//            'username' => '9360390070',
//            'otp' => '291991',
////            'otp_verifier' => 'otp ver',
//            'client_id' => config('passport.password_grant_client.id'),
//            'client_secret' => config('passport.password_grant_client.secret'),
//            'grant_type' => 'otp_grant'
//        ]);
//
//
//        $result = json_decode($response->getBody(), true);
//        dd($result);
//        $a = now()->toDateTimeString();
//        dd($a);
//        $url = "https://ippanel.com/services.jspd";
//
//        $rcpt_nm = array('9372283246');
//        $param = array
//        (
//            'uname'=>'09141492090',
//            'pass'=>'1680162675Behrad!',
//            'from'=>'+9890005476',
//            'message'=>'تست',
//            'to'=>json_encode($rcpt_nm),
//            'op'=>'send'
//        );
//
//        $handler = curl_init($url);
//        curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "POST");
//        curl_setopt($handler, CURLOPT_POSTFIELDS, $param);
//        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
//        $response2 = curl_exec($handler);
//
//        $response2 = json_decode($response2);
//        $res_code = $response2[0];
//        $res_data = $response2[1];
//        dd($response2);


//// Get current time in Tehran timezone
//        $now = Carbon::now('Asia/Tehran');
//
//// Create a Carbon object for the end of the day (23:59) in Tehran timezone
//        $endOfToday = $now->clone()->setTime(23, 59);
//
//// Calculate the difference in minutes
//        $minutesRemaining = $endOfToday->diffInMinutes($now);
////        dd($minutesRemaining);
//        $start = now();
//        $hadis = Cache::get('hadis_data');
//        $event = Cache::get('event');
//
//        if (!$hadis || !$event) {
//            $pools=Http::pool(fn(Pool $pool)=>[
//                $pool->get('https://api.keybit.ir/hadis'),
//                $pool->get('https://one-api.ir/time/?token=712671:661250a3a5a8a&action=date&date=today'),
//
//            ]);
//            Cache::put('hadis_data', $pools[0]->body(), $minutesRemaining);
//            Cache::put('event', $pools[1]->body(), $minutesRemaining);
//        }
//
//
//
//
//        dd(now()->diffInMilliseconds($start),[Cache::get('hadis_data'),Cache::get('event')]);


//        try {
//            $user = User::find(1);
//            $user->notify(new TestNotification());
//            \Notification::send($user, new TestNotification());
//            Sms::via('farazsms')->send('این یک پیام تست برای ارسال پیامک از فرازاس ام اس است', function($sms) {
//                $sms->to(['09372283246']);
//            });
//        } catch (InvalidMessageException $exception) {
//            dd($exception->getMessage());
//        }
//        $jalaliDate = JalaliCalendar::fromGregorian('2020-05-24');
//        dd($jalaliDate);
    }
}
