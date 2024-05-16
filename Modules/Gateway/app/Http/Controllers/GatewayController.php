<?php

namespace Modules\Gateway\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Gateway\app\Http\Traits\PaymentRepository;
use Modules\OUnitMS\app\Models\VillageOfc;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;
use \Modules\Gateway\app\Models\Payment as PG;

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
        $list = $user->load('person')->payments()
            ->with(['status','organizationUnit']) // Eager load the 'status' relationship
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
            $user = \Auth::user();
            $user->load(['organizationUnits' => function ($query) {
                $query->where('unitable_type', VillageOfc::class)->whereDoesntHave('payments')->with(['unitable']);
            }]);
            if (is_null($user->organizationUnits)) {
                return response()->json(['message' => 'شما مجاز به پرداخت نمی باشید'], 403);
            }

//            $vills=$user->o
            $result = $this->generatePayGate($user);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
            return response()->json(['message' => 'خطا در اتصال یه درگاه بانکی'], 500);
        }


    }

    public function verifyPayment(Request $request)
    {

        $data = $request->all();
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

//            $payment = PG::where('authority', $request->authority)->first();
            $payments = $user->payments()->where('authority', $request->authority)->get();
            $status = PG::GetAllStatuses()->where('name', 'پرداخت شده')->first();
            $user->load(['organizationUnits' => function ($query) {
                $query->where('unitable_type', VillageOfc::class)->with('unitable');
            }]);
            $degs = $user->organizationUnits->pluck('unitable.degree');
//                ->reject(function ($dg) {
//                return $dg === null;
//            });

            $amount = 0;
            $degs->each(function ($deg) use (&$amount) {
                $deg = (int)$deg;

//            $currentAmount = 0; // Initialize a variable for current increment
                $currentAmount = match ($deg) {
                    1, 2 => 400000,
                    3, 4 => 600000,
                    5, 6 => 850000,
                    default => 0,
                };

                $amount += $currentAmount;
            });

            $receipt = Payment::amount($amount)->transactionId($request->authority)->verify();

            // You can show payment referenceId to the user.
            $transactionid = $receipt->getReferenceId();

            $payments->each(function (PG $payment) use ($transactionid, $receipt, $status) {
                $payment->transactionid = $transactionid;
                $payment->purchase_date = $receipt->getDate();
                $payment->status_id = $status->id;
                $payment->save();
            });
            $user->load('person');

            $factor = [
                'transactionid' => $transactionid,
                'purchase_date' => $receipt->getDate(),
                'amount'=>$amount,
                'status'=>$status,
                'person' => $user->person,

            ];

            return response()->json(['data' => $factor, 'message' => 'پرداخت شما با موفقیت انجام شد']);

        } catch (InvalidPaymentException $exception) {
            if ($exception->getCode() == 101) {
                $user?->load('person');
                $factor = [
                    'transactionid' => $payments[0]->transactionid,
                    'purchase_date' => $payments[0]->purchase_date,
                    'amount'=>$amount,
                    'status'=>$status,
                    'person' => $user->person,

                ];
                return response()->json(['message' => $exception->getMessage(), 'data' => $factor ?? null]);
            } elseif ($exception->getCode() == -51) {
                $status = PG::GetAllStatuses()->where('name', 'پرداخت ناموفق')->first();
                $payments->each(function (Payment $payment) use ( $status) {
                    $payment->status_id = $status->id;
                    $payment->save();
                });

            }

            return response()->json(['message' => $exception->getMessage()], 400);

        }
    }
}
