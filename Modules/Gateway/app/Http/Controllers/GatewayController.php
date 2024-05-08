<?php

namespace Modules\Gateway\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;
use \Modules\Gateway\app\Models\Payment as PG;

class GatewayController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $page = $request->pageNum ?? 1;
        $perPage = $request->perPage ?? 10;
        $list = PG::with('status', 'user')->paginate($perPage, page: $page);

        return response()->json($list);
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

            $amount = 1000;
            $invoice = (new Invoice)->amount($amount);


            return Payment::via('zarinpal')->purchase($invoice, function ($driver, $transactionId) use ($user, $amount) {

                $status = PG::GetAllStatuses()->where('name', 'در انتظار پرداخت')->first();


                $payment = new PG();
                $payment->user_id = $user->id;
                $payment->authority = $transactionId;
                $payment->amount = $amount;
                $payment->status = $status->id;
                $payment->save();
            })->pay()->toJson();
        } catch (\Exception $e) {
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

            $payment = PG::where('authority', $request->authority)->first();
            $status = PG::GetAllStatuses()->where('name', 'پرداخت شده')->first();

            $receipt = Payment::amount(1000)->transactionId($request->authority)->verify();

            // You can show payment referenceId to the user.
            $transactionid = $receipt->getReferenceId();

            $payment->transactionid = $transactionid;
            $payment->purchase_date = $receipt->getDate()->timestamp;
            $payment->status = $status->id;
            $payment->save();


            return response()->json(['message' => 'پرداخت شما با موفقیت انجام شد']);

        } catch (InvalidPaymentException $exception) {
            if ($exception->getCode() == 101 && isset($receipt) && isset($payment) && is_null($payment->transactionid)) {
                $payment->transactionid = $transactionid;
                $payment->purchase_date = $receipt->getDate()->timestamp;
                $payment->status = $status->id;
                $payment->save();

                return response()->json(['message' => $exception->getMessage()]);
            }

            return response()->json(['message' => $exception->getMessage()],400);

        }
    }
}
