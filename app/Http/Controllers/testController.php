<?php

namespace App\Http\Controllers;


use Modules\AAA\app\Models\User;
use Modules\EvalMS\app\Models\Evaluation;
use Modules\EvalMS\app\Models\Evaluator;
use Modules\Gateway\app\Http\Traits\PaymentRepository;
use Modules\Gateway\app\Models\Payment as PG;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\VillageOfc;
use Modules\PersonMS\app\Models\Person;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Payment\Facade\Payment;


class testController extends Controller
{
    use PaymentRepository;

    public function run()
    {
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
