<?php

namespace App\Http\Controllers;


use Modules\AAA\app\Models\User;
use Modules\EvalMS\app\Models\Evaluation;
use Modules\EvalMS\app\Models\Evaluator;
use Modules\Gateway\app\Http\Traits\PaymentRepository;
use Modules\Gateway\app\Models\Payment as PG;
use Modules\HRMS\app\Http\Enums\OunitCategoryEnum;
use Modules\HRMS\app\Http\Traits\PositionTrait;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\VillageOfc;
use Modules\PersonMS\app\Models\Person;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Payment\Facade\Payment;


class testController extends Controller
{
    use PaymentRepository,PositionTrait;

    public function run()
    {

        $usersWithReadNotificationsButNoPaymentsWithStatus46 = User::whereHas('notifications'
//  , function ($query) {
//             $query->whereNotNull('read_at');
//         }
        )->whereDoesntHave('payments'
            , function ($query) {
                $query->where('status_id', 46);
            }
        )->with('person','organizationUnits.ancestors','payments')->get();
        // dd($usersWithReadNotificationsButNoPaymentsWithStatus46->pluck('id'));
        $html = '<table>';
        $html .= '<tr><th>دخیار</th><th>دهیاری</th><th>کد آبادی</th><th>شهرستان</th></tr>';

        foreach ($usersWithReadNotificationsButNoPaymentsWithStatus46 as $user) {
            foreach ($user->organizationUnits as $unit) {
                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars($user->person->display_name) . '</td>';
                $html .= '<td>' . htmlspecialchars($unit->name) . '</td>';
                $html .= '<td>' . htmlspecialchars($unit->unitable->abadi_code) . '</td>';
                $html .= '<td>' . htmlspecialchars($unit->ancestors[2]->name) . '</td>';
                $html .= '</tr>';
            }
            // If the user has no organization units, print just the user's name with "No Units"
            if (count($user->organizationUnits) == 0) {
                $html .= '<tr><td>' . htmlspecialchars($user->person->display_name) . '</td><td>No Units</td></tr>';
            }
        }

        $html .= '</table>';

        echo $html;
//        $organizationUnitIds = OrganizationUnit::whereHas('payments', function ($query) {
//            $query->where('status_id', 46)
//                ->where('user_id','!=',1905);
//        })->with('head.person','ancestors','unitable')->get();
//
//// Start the table
//        $html = '<table>';
//        $html .= '<tr><th>دهیاری</th><th>نام دهیار</th><th>شهرستان</th><th>کد آبادی</th></tr>';
//
//// Loop through the data and add it to the table
//        foreach ($organizationUnitIds as $organizationUnit) {
//            $html .= '<tr>';
//            $html .= '<td>' . htmlspecialchars($organizationUnit->name) . '</td>';
//            $html .= '<td>' . htmlspecialchars($organizationUnit->head->person->display_name) . '</td>';
//            $html .= '<td>' . htmlspecialchars($organizationUnit->ancestors[2]->name) . '</td>';
//            $html .= '<td>' . htmlspecialchars($organizationUnit->unitable->abadi_code) . '</td>';
//            $html .= '</tr>';
//        }
//
//// End the table
//        $html .= '</table>';
//
//// Print the table
//        echo $html;
//        try {
//
////            $payment = PG::where('authority', $request->authority)->first();
//            $authority='A00000000000000000000000000015og61v2';
//            $user = User::find(1770);
//            $payments = $user->payments()->where('authority', $authority)->with('organizationUnit.unitable')->get();
//
//            $status = PG::GetAllStatuses()->where('name', 'پرداخت شده')->first();
//            $user->load(['organizationUnits' => function ($query) {
//                $query->where('unitable_type', VillageOfc::class)->with('unitable');
//            }]);
//            $degs = $payments->pluck('organizationUnit.unitable.degree');
////                ->reject(function ($dg) {
////                return $dg === null;
////            });
//
//            $amount = 0;
//            $degs->each(function ($deg) use (&$amount) {
//                $deg = (int)$deg;
//
////            $currentAmount = 0; // Initialize a variable for current increment
//                $currentAmount = match ($deg) {
//                    1 => 350000,
//                    2 => 450000,
//                    3 => 500000,
//                    4 => 600000,
//                    5 => 700000,
//                    6 => 750000,
//                    default => 0,
//                };
//
//                $amount += $currentAmount;
//            });
//
//            $receipt = Payment::amount($amount)->transactionId($authority)->verify();
//
//            // You can show payment referenceId to the user.
//            $transactionid = $receipt->getReferenceId();
//
//            $payments->each(function ($payment) use ($transactionid, $receipt, $status) {
//                $payment->transactionid = $transactionid;
//                $payment->purchase_date = $receipt->getDate();
//                $payment->status_id = $status->id;
//                $payment->save();
//            });
//            $user->load('person');
//
//            $factor = [
//                'transactionid' => $transactionid,
//                'purchase_date' => $receipt->getDate(),
//                'amount'=>$amount,
//                'status'=>$status,
//                'person' => $user->person,
//
//            ];
//
//            return response()->json(['data' => $factor, 'message' => 'پرداخت شما با موفقیت انجام شد']);
//
//        }
//        catch (InvalidPaymentException $exception) {
//            if ($exception->getCode() == 101) {
//                $user?->load('person');
//                $factor = [
//                    'transactionid' => $payments[0]->transactionid,
//                    'purchase_date' => $payments[0]->purchase_date,
//                    'amount'=>$amount,
//                    'status'=>$status,
//                    'person' => $user->person,
//
//                ];
//                return response()->json(['message' => $exception->getMessage(), 'data' => $factor ?? null]);
//            } elseif ($exception->getCode() == -51) {
//                $status = PG::GetAllStatuses()->where('name', 'پرداخت ناموفق')->first();
//                $payments->each(function ($payment) use ( $status) {
//                    $payment->status_id = $status->id;
//                    $payment->save();
//                });
//
//            }
//
//            return response()->json(['message' => 'درصورت بروز مشکل با پشتیبانی تماس بگیرید'], 400);
//
//        }
        \DB::enableQueryLog();

//        $a = User::whereHas('person', function ($q) {
//            $q->where('national_code','2840127121')
//            ->whereHas('employee');
//})->with(['latestRecruitmentScript','person.employee'])->first();

//        $c = RecruitmentScript::whereDoesntHave('latestStatus',
////            function ($query) {
////            $query->where('statuses.name','!=','غیرفعال')
////                 ->latest('create_date')->take(1);
////
////        }
//        )
//            ->find( 2400);


//        $a = Person::where('national_code', '2840127121');
//            $a->whereHas('latestRecruitmentScript', function ($query) {
//                $query->where('expire_date', '>', now())
//                    ->whereDoesntHave('latestStatus',function ($query) {
//                        $query->where('name','=','غیرفعال');
//                    });
//
//            })
////            ->has('employee')
//            ->first();
//->whereHas('latestRecruitmentScript', function ($query) {
//
//            $query->where('expire_date', '>', now())
////            $query->where('create_date', '<', now())
//            ->whereDoesntHave('latestStatus',function ($query) {
//                $query->where('name','=','غیرفعال');
//            });
//
//        })->with(['latestRecruitmentScript'])->first();
        $employee = Employee::find(1905);
        $employee->load(['latestRecruitmentScript'=>function ($query) {
//            $query->where('expire_date', '>', now())
            $query->where('create_date', '<', now())

                ->whereDoesntHave('latestStatus',function ($query) {
                    $query->where('name','=','غیرفعال');
                })
//                ->with('issueTime')
            ;
        }]);
        $b = \DB::getQueryLog();

        dd($employee,$b);
    }
}
