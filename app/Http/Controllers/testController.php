<?php

namespace App\Http\Controllers;


//use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\AAA\app\Models\Permission;
use Modules\AAA\app\Models\Role;
use Modules\AAA\app\Models\User;
use Modules\OUnitMS\app\Models\CityOfc;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;

use Modules\EvalMS\app\Http\Repositories\EvaluatorRepository;
use Modules\EvalMS\app\Models\Evaluation;
use Modules\EvalMS\app\Models\Evaluator;
use Modules\OUnitMS\app\Models\TownOfc;
use Modules\OUnitMS\app\Models\VillageOfc;
use Modules\WidgetsMS\app\Http\Repositories\WidgetRepository;
use PDO;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Invoice;
use Str;
use Shetabit\Payment\Facade\Payment;

class testController extends Controller
{
    public function callback(Request $request)
    {

        try {
            $a = 1;
            $receipt = Payment::amount(1000)->transactionId($request->Authority)->verify();

            // You can show payment referenceId to the user.
            echo $receipt->getReferenceId();
            echo 'verify';

        } catch (InvalidPaymentException $exception) {
            /**
            when payment is not verified, it will throw an exception.
            We can catch the exception to handle invalid payments.
            getMessage method, returns a suitable message that can be used in user interface.
             **/
            dd($a);
            echo $exception->getMessage();
        }
//        dd($request->all());
    }

    public function run()
    {

        $invoice = (new Invoice)->amount(1000);
        return Payment::via('zarinpal')->purchase($invoice, function ($driver, $transactionId) use ($invoice) {
//            dd(get);

            // Store transactionId in database as we need it to verify payment in the future.
        })->pay()->render();
//        $user = User::find(1);
//        $activeWidgets = $user->activeWidgets;
//
//        $allPermissions = $activeWidgets->map(function ($widget) {
//            return $widget->permission->slug; // Extract permission model
//        });
//
//        $functions = WidgetRepository::extractor($allPermissions->toArray());
//
//        $widgetData = [];
//        foreach ($functions as $key => $item) {
//
//            $widgetData[] = [
//                'name' => Str::replace('/', '', $key),
//                'data' => call_user_func([$item['controller'], $item['method']
//                    ]
//                    , $user)];
//        }
//        dd($widgetData);
//        $a = OrganizationUnit::where('head_id', '!=', null)->get('id');
//        $b = Evaluation::find(1)->organizationUnits()->sync($a);
//        dd($a);
//        \Artisan::call('module:seed AAA');
//        $ounit = OrganizationUnit::findOrFail(3856);
//
////        $usersUnits = EvaluatorRepository::getOunits($user, loadHeads: true);
//
//        $whoToFill = EvaluatorRepository::getOunitsParents($ounit);
////        dd($whoToFill);
//
//
//        $filteredModels = $whoToFill->filter(function ($model) {
//            return $model->organizationunit->head && $model->organizationunit->head->id === 1;
//        });
//
//        $highestIndex = $filteredModels->keys()->max();
//
//        $filteredCollection = $whoToFill->filter(function ($model, $index) use ($highestIndex) {
//            return $index <= $highestIndex;
//        });
//        dd($filteredCollection);
//        $city = OrganizationUnit::with('unitable')->where('name', 'پلدشت')->where('unitable_type', CityOfc::class)->first();
////        dd($city);
//
//        $dcName = 'مرکزی';
//
//        $district = DistrictOfc::whereHas('organizationUnit', function ($query) use ($dcName) {
//            $query->where('name', $dcName);
//        })->where('city_ofc_id', $city->unitable->id)->first();
////        dd($district->organizationUnit);
//        $townName = 'چایپاسارشرقی';
//
//        $town = TownOfc::whereHas('organizationUnit', function ($query) use ($townName) {
//            $query->where('name', $townName);
//        })->where('district_ofc_id', $district->id)->first();
////        dd($town->organizationUnit);
//
//        $villName = 'پزیک';
//
//        $village = VillageOfc::whereHas('organizationUnit', function ($query) use ($villName) {
//            $query->where('name', $villName);
//        })->where('town_ofc_id', $town->id)->first();
//
//        dd(OrganizationUnit::where('name',$villName)->first());
//        $connection = DB::connection()->getPdo();
//        $statement = $connection->prepare('SHOW TABLES');
//        $statement->execute();
//        $tables = $statement->fetchAll(PDO::FETCH_COLUMN);
//        $tableString = implode(', ', $tables);
//
//        echo $tableString;
//        $user = User::find(1);
////
//        $sidebarPermissions = $user->permissions()->where('permission_type_id', '=', 1)->with('moduleCategory')->get();
//        dd($sidebarPermissions->pluck('moduleCategory'));
//        $pers = Permission::all(['id']);
//////
//        $role = Role::find(1);
//        $role->permissions()->sync($pers);

//        $phoneNumber = "09148007792";
//        $trimmedPhoneNumber = ltrim($phoneNumber, "0");
//        dd($trimmedPhoneNumber);
//        $user = User::find(57);
//
//        $usersUnits = EvaluatorRepository::getOunits($user, loadHeads: true);
//        $x = $usersUnits[73];
//        dd($x);
//        $headIDs = $usersUnits->pluck('organizationUnit.head.id')->reject(function ($head) {
//            return $head === null;
//        })->unique()->toArray();
//        $unitIDs = $usersUnits->pluck('organizationUnit.id')->reject(function ($head) {
//            return $head === null;
//        })->unique()->toArray();
//
//        $pageNum = $request->pageNum ?? 1;
//        $perPage = $request->perPage ?? 10;

//        $result = EvaluatorRepository::getEvalOunitHistory(1, 3669,$unitIDs, $headIDs, $pageNum, $perPage);
//        dd($result['data']->parameters[0]);
////        $b = OrganizationUnit::where('unitable_type', VillageOfc::class)->get(['id']);
////        $e = Evaluation::find(1);
////        $e->organizationUnits()->sync($b);
////        dd($b);
////        $user = User::find(1);
////        $usersUnits= EvaluatorRepository::getOunits($user);
//////        dd($usersUnits);
//////        $headIDs = $usersUnits->pluck('organizationUnit.head.id')->reject(function ($head) {
//////            return $head === null;
//////        })->unique()->toArray();
////        $unitIDs = $usersUnits->pluck('organizationUnit.id')->toArray();
////        dd($unitIDs);
////        $b = EvaluatorRepository::evalOfOunits($unitIDs, 1);
////        dd($unitIDs);
//        // Eager load evaluators with specific organization units and their person data
//$r=[
//    // Eager load parameters with answers and filter evaluators
//    'parameters' => function ($query) {
//        $query->with([
//            'evalParameterAnswers' => function ($query) {
//                $query->with([
//                    'evaluator' => function ($query) {
//                        $query->whereIn('user_id', [1, 20, 30])
//                            ->whereIn('organization_unit_id', [2547, 3856]);
//                    },
//                    'evaluator.person' // Already eager loaded in evaluation
//                ]);
//            }
//        ])->paginate(10,['*'],'page',1);
//    }
//];
//        if (true) {
//            $r['evaluators'] =
//                 function ($query) {
//                    $query->whereIn('organization_unit_id', [2547, 3856])
//                        ->with('organizationUnit.person');
//                };
//                }
//            ;
//
//        $evaluation = Evaluation::with($r)
//            ->find(1);
//
//        dd($evaluation);
////        $evaluation = Evaluation::with('parameters')->find(1);
//        $evaluation = Evaluation::with(['evaluators' => function ($query) {
//            $query->whereIn('organization_unit_id', [2547, 3856])->with('organizationUnit.person');
//        }])->find(1);
//
//
//
//        $parameters = $evaluation->parameters()->with(['evalParameterAnswers.evaluator' => function ($query) {
//            $query->whereIn('user_id', [1, 20, 30])
//                ->whereIn('organization_unit_id', [2547, 3856]);
//        }])->with('evalParameterAnswers.evaluator.person')->paginate(10);
////        $parameters = Evaluation::with('parameters.evalParameterAnswers.evaluator.person')->find(1)->paginate(10);
//
//        dd($evaluation, $parameters->getCollection()->toArray());
//        $filteredParameters = $evaluation->parameters()
//            ->with('evalParameterAnswers.evaluator')
//            ->whereHas('evalParameterAnswers.evaluator', function ($query) {
//                $query->whereIn('user_id', [1, 20, 30])
//                    ->whereIn('organization_unit_id', [2547, 3856]);
//            })
//            ->paginate(1); // Adjust page size as needed
//        dd($filteredParameters->getCollection()->toArray());
//
////        $userx = User::find(1);
////        $a = EvaluatorRepository::getOunitsWithSubsOfUser($userx, loadHeads: true);
//////        dd($a);
////        $b = $a->pluck('organizationUnit.head.id')->reject(function ($head) {
////            return $head === null;
////        })->unique();
////
////        $formarz=Evaluation::with(['parameters.evalParameterAnswers.evaluator'=>function ($query) {
////$query->whereIn('user_id', [1, 20, 30])
////    ->whereIn('organization_unit_id', [2547,3856])
////->with(['organizationUnit','person'])
////            ->withDefault();
////        }])->find(1)->paginate(10);
////        dd($formarz);
//
////        dd($b);
////        $aa = OrganizationUnit::with(['head.evaluator' => function ($query) use ($b) {
////            $query->where('evaluation_id', 1)->whereIn('user_id',$b)->with('evalParameterAnswers.evalParameter');
////        }])->find(3856);
////        $evaluators=Evaluator::where('evaluation_id', 1)->where('organization_unit_id',3856)->whereIn('user_id',[1,2,3])->with(['evalParameterAnswers.evalParameter'])->get();
////        $grouped = $evaluators->groupBy(function ($evaluator) {
////            dd($evaluator);
////            return $evaluator->evalParameter;
////        })->map(function ($group) {
////            return $group->pluck('evalParameterAnswers');
////        });
////        dd($grouped);
//        /**
//         * @var OrganizationUnit $head
//         */
////        $x = $a->each(function ($head) {
////            $head->load(['organizationUnit.head.evaluators' => function ($query) {
////                $query->where('evaluation_id', 1)->where('organization_unit_id',1)->with('evalParameterAnswers.evalParameter');;
////            }]);
////        });
//
////        $y = json_encode($x->toArray());
////        echo '<pre>' . var_export($x->toArray(), true) . '</pre>';
////        exit();
////        dd($x);
////dd($x->each(function ($head) {
////            $head->organizationUnit->head->evaluators->groupBy(function ($evaluator) {
////                return $evaluator->evalParameterAnswers->pluck('evalParameter.name')->implode(',');
////            });
////        })->toArray());
//        $c = EvaluatorRepository::evalOfOunits($b->toArray(), 1);
//        dd(json_encode($c));
//
////        $eval = Evaluation::with(['organizationUnits'=>function ($query) {
////            $query->whereIn('organization_unit_id', [1, 2, 3, 4]);
////        }])->find(1);
////        dd($eval);
////        $ous = $user->organizationUnits;
////        dd($ous);
//        $ou = OrganizationUnit::find(3856);
//        $parents = [];
//        /**
//         * @var VillageOfc $model
//         */
//
////        foreach ($ous as $key => $ou) {
////            dd($ou);
//            $model = $ou->unitable()->with(['organizationUnit.head','organizationUnit.evaluations'])->first();
////        while ($model instanceof StateOfc === false) {
//            while (method_exists($model, 'parent') === true) {
//                $parents[] = $model;
//                $model = $model->parent()->with(['organizationUnit.head','organizationUnit.evaluations'])->first();
////            dd($model);
//
//            }
//            $parents[] = $model->load(['organizationUnit.head','organizationUnit.evaluations']);
//////
////        }
//        dd($parents);

////        $a = collect($parents)->flatten(1)->unique();
////
////        $b = $a->pluck('organizationUnit')->flatten(2);
////
////        dd($b);
////            ->pluck('head');
//
////        $b = $a->pluck('organizationUnit')
////            ->pluck('head')
////            ->reject(function ($id) {
////                return $id === null;
////            })->unique();
//
////User::doesntHave()
////        dd($b);
////        dd($parents);
//
////        $user = User::find(1);
////        dd($user);
////        $user->load('person.personable.homeAddress', 'person.avatar', 'person.workForce.workforceable');
////        dd($user);
////        $workForce = $user->person->workForce;
////
////        if ($workForce->workforceable_type === Employee::class) {
////            $rs = $workForce->workforceable->recruitmentScripts;
////        }
////        $data = [
////            'info' => $user->person,
////            'recruitmentScripts' => $rs ?? null,
////
////        ];
////        $user->load('person.workForce.workForceable');
////        $workForce = $user->person->workForce->workForceable;
////        if ($workForce instanceof Employee) {
////            $rs = $workForce->recruitmentScripts;
////        }
////        $notif=$user->notifications()->where('type', '=', VerifyInfoNotification::class)->first();
////        if (is_null($notif)) {
////            $user->notify(new VerifyInfoNotification());
////            return $user->notifications()->where('type', '=', VerifyInfoNotification::class)->first();
////        } elseif (!$notif->read()) {
////            $notif->markAsRead();
////            dd( $notif);
////        }
////        $a = convertToDbFriendly('٦٦٦٦٦ ميزان تحقق بودجه مصوب دهياري');
////        dd($a);
////        $villages = json_decode(file_get_contents(realpath(__DIR__ . '/agh.json')), true);
//////        $a = [];
////        $records = array_reduce($villages, function ($records, $village) {
////            $records[$village[0]][$village[1]][$village[2]][] = $village[3];
////            return $records;
////        }, []);
////        dd($records);
//
////        $districtsNotFound = [];
////        $townNotFound = [];
////        $villageNotFound = [];
////
////
////
////
////        foreach ($records as $key => $record) {
////            $city = City::where('name', $key)->first();
////            if (!is_null($city)) {
////                foreach ($record as $district => $towns) {
////                    $dist = District::where('name', $district)->where('city_id',$city->id)->first();
////
////                    if (is_null($dist)) {
////                        $districtsNotFound[] = $district;
////                    }else{
////                        foreach ($towns as $town => $villages) {
////                            $twn = Town::where('name', $town)->where('district_id', $dist->id)->first();
////                            if (is_null($twn)) {
////                                $townNotFound[$key][$district][] = $town;
////                            }else{
////                                foreach ($villages as $village) {
////                                    $vlg = Village::where('name', $village)->where('town_id', $twn->id)->first();
////                                    if (is_null($vlg)) {
////                                        $villageNotFound[$key][$district][$town][] = $village;
////
////                                    }
////                                }
////                            }
////                        }
////                    }
////
////
////                }
////            }
////        }
////        dd($districtsNotFound,$townNotFound,$villageNotFound);
////        foreach ($villages as $village) {
////
////            $a[$village[0]][$village[1]][$village[2]][] = $village[3];
////        }
////        dd($a);
////        dd($moduleCategoriesData);
//
//
////        $user = User::where('mobile', '9360390070')->first();
////
////        $a = OtpRepository::verify($user->id, 29199);
////        dd($a);
//
//
////        $baseUrl = url('/');
////
////        $response = Http::post("{$baseUrl}/oauth/token", [
////            'username' => '9360390070',
////            'otp' => '291991',
//////            'otp_verifier' => 'otp ver',
////            'client_id' => config('passport.password_grant_client.id'),
////            'client_secret' => config('passport.password_grant_client.secret'),
////            'grant_type' => 'otp_grant'
////        ]);
////
////
////        $result = json_decode($response->getBody(), true);
////        dd($result);
////        $a = now()->toDateTimeString();
////        dd($a);
////        $url = "https://ippanel.com/services.jspd";
////
////        $rcpt_nm = array('9372283246');
////        $param = array
////        (
////            'uname'=>'09141492090',
////            'pass'=>'1680162675Behrad!',
////            'from'=>'+9890005476',
////            'message'=>'تست',
////            'to'=>json_encode($rcpt_nm),
////            'op'=>'send'
////        );
////
////        $handler = curl_init($url);
////        curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "POST");
////        curl_setopt($handler, CURLOPT_POSTFIELDS, $param);
////        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
////        $response2 = curl_exec($handler);
////
////        $response2 = json_decode($response2);
////        $res_code = $response2[0];
////        $res_data = $response2[1];
////        dd($response2);
//
//
////// Get current time in Tehran timezone
////        $now = Carbon::now('Asia/Tehran');
////
////// Create a Carbon object for the end of the day (23:59) in Tehran timezone
////        $endOfToday = $now->clone()->setTime(23, 59);
////
////// Calculate the difference in minutes
////        $minutesRemaining = $endOfToday->diffInMinutes($now);
//////        dd($minutesRemaining);
////        $start = now();
////        $hadis = Cache::get('hadis_data');
////        $event = Cache::get('event');
////
////        if (!$hadis || !$event) {
////            $pools=Http::pool(fn(Pool $pool)=>[
////                $pool->get('https://api.keybit.ir/hadis'),
////                $pool->get('https://one-api.ir/time/?token=712671:661250a3a5a8a&action=date&date=today'),
////
////            ]);
////            Cache::put('hadis_data', $pools[0]->body(), $minutesRemaining);
////            Cache::put('event', $pools[1]->body(), $minutesRemaining);
////        }
////
////
////
////
////        dd(now()->diffInMilliseconds($start),[Cache::get('hadis_data'),Cache::get('event')]);
//
//
////        try {
////            $user = User::find(1);
////            $user->notify(new TestNotification());
////            \Notification::send($user, new TestNotification());
////            Sms::via('farazsms')->send('این یک پیام تست برای ارسال پیامک از فرازاس ام اس است', function($sms) {
////                $sms->to(['09372283246']);
////            });
////        } catch (InvalidMessageException $exception) {
////            dd($exception->getMessage());
////        }
////        $jalaliDate = JalaliCalendar::fromGregorian('2020-05-24');
////        dd($jalaliDate);
    }
}
