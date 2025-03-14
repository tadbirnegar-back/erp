<?php

namespace Modules\Gateway\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Gateway\app\Http\Traits\PaymentRepository;
use Modules\Gateway\app\Jobs\VerifyPaymentJob;
use Modules\Gateway\app\Models\Payment as PG;
use Modules\LMS\app\Http\Services\VerificationPayment;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\PayStream\app\Models\Online;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Payment\Facade\Payment;

class GatewayController extends Controller
{
    use PaymentRepository;

    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $user = \Auth::user();

//        $page = $request->pageNum ?? 1;
//        $perPage = $request->perPage ?? 10;
//        $list = PG::with('status', 'user')->paginate($perPage, page: $page);
        $list = $user->load('person.personable')->payments()
            ->whereHas('status', function ($query) {
                $query->where('name', 'پرداخت شده');
            })
            ->with(['status', 'organizationUnit.unitable', 'organizationUnit.ancestorsAndSelf']) // Eager load the 'status' relationship
            ->get();
//        ->paginate($perPage, page: $page);
        return response()->json(['person' => $user->person, 'payments' => $list]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    public function startPayment(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = \Auth::user();
            $user->load(['organizationUnits.unitable', 'organizationUnits.payments' => function ($q) {
                $q->whereHas('status', function ($query) {
                    $query->where('name', 'پرداخت شده');
                });
            }]);


            if ($user->organizationUnits->isEmpty()) {
                return response()->json(['message' => 'شما مجاز به پرداخت نمی باشید'], 403);
            }

            $result = $this->generatePayGate($user);

            $url = $result->getAction();
            $url = rtrim($url, '/');

            $urlArray = explode('/', $url);
            $authority = end($urlArray);
            DB::commit();

            dispatch(new VerifyPaymentJob($authority))
                ->onQueue('high')
                ->delay(now()->addMinutes(12));


            return response()->json($result);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در اتصال یه درگاه بانکی', 'error', 'error'], 500);
        }


    }

    public function verifyPayment(Request $request)
    {

        $data = $request->all();

        $online = Online::where('authority', $data['authority'])->first();
        if (!empty($online)) {
            $validator = Validator::make($data, [
                'authority' => [
                    'required',
                    'exists:onlines,authority'
                ]
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $online = Online::where('authority', $data['authority'])->first();
            try {
                \Illuminate\Support\Facades\DB::beginTransaction();
                $verify = new VerificationPayment($online);
                $result = $verify->verifyPayment();
                DB::commit();
                return response()->json($result);
            } catch (\Exception $exception) {
                DB::rollBack();
                DB::beginTransaction();
                $verify = new VerificationPayment($online);
                $result = $verify->DeclinePayment();
                DB::commit();
                return response()->json($result);
            }
        } else {
            $user = \Auth::user();


            $validator = Validator::make($data, [
                'authority' => [
                    'required',
                    'exists:payments,authority'
                ]
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            try {

                $payments = $user->payments()->where('authority', $request->authority)->with('organizationUnit.unitable')->get();

                $status = PG::GetAllStatuses()->where('name', 'پرداخت شده')->first();

                $amount = 0;
                $total = $payments->sum('amount');

                $receipt = Payment::amount($total)->transactionId($request->authority)->verify();

                // You can show payment referenceId to the user.
                $transactionid = $receipt->getReferenceId();

                $payments->each(function ($payment) use ($transactionid, $receipt, $status) {
                    $payment->transactionid = $transactionid;
                    $payment->purchase_date = $receipt->getDate();
                    $payment->status_id = $status->id;
                    $payment->save();
                });
                $user->load('person');

                $factor = [
                    'transactionid' => $transactionid,
                    'purchase_date' => $receipt->getDate(),
                    'amount' => $total,
                    'status' => $status,
                    'person' => $user->person,

                ];

                return response()->json(['data' => $factor, 'message' => 'پرداخت شما با موفقیت انجام شد']);

            } catch (InvalidPaymentException $exception) {
                if ($exception->getCode() == 101) {
                    $user?->load('person');
                    $factor = [
                        'transactionid' => $payments[0]->transactionid,
                        'purchase_date' => $payments[0]->purchase_date,
                        'amount' => $amount,
                        'status' => $status,
                        'person' => $user->person,

                    ];
                    return response()->json(['message' => $exception->getMessage(), 'data' => $factor ?? null]);
                } elseif ($exception->getCode() == -51) {
                    $status = PG::GetAllStatuses()->where('name', 'پرداخت ناموفق')->first();
                    $payments->each(function ($payment) use ($status) {
                        $payment->status_id = $status->id;
                        $payment->save();
                    });

                }

                return response()->json(['message' => 'درصورت بروز مشکل با پشتیبانی تماس بگیرید'], 400);

            }
        }


    }

    public function paymentsPerDistrict(Request $request)
    {
        $user = auth()->user();
        if ($request->districtID) {
            $ounit = OrganizationUnit::find($request->districtID);
        } else {
            $ounit = $user->organizationUnits[0];
        }


        $villages = $ounit->descendants()->whereDepth(2)->with(['head.person',
            'head.notifications',
            'unitable',
//            'ancestors',
            'payments' => function ($query) {
                $query->where('status_id', '=', '46');
            }, 'evaluator'])->get();

        $b = collect($this->calculatePrice($villages)['ounits']);
        $c = $b->map(function ($item) {
            $phase1 = ($item['alreadyPayed'] > 0);
            $phase2 = ($item['price'] <= 0 && $item['alreadyPayed'] > 0);
            $verified = $item['ounitObject']?->head?->notifications->isNotEmpty() && $item['ounitObject']?->head?->notifications[0]->read_at != null;
            $hasEval = $item['ounitObject']?->evaluator != null;
            /**
             * @var OrganizationUnit $ou
             */
            $ou = $item['ounitObject'];
            $ou->setAttribute('payStat', [
                'phase_1' => $phase1,
                'phase_2' => $phase2,
                'verified' => $verified,
                'hasEval' => $hasEval,
                'person' => $item['ounitObject']?->head?->person,

            ]);
            unset($ou->head);
            unset($ou->payments);
            unset($ou->evaluator);

            return $ou;
        });
        return response()->json($c);
    }
}
