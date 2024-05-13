<?php

namespace Modules\Gateway\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Gateway\app\Http\Traits\PaymentRepository;
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
            ->with(['status']) // Eager load the 'status' relationship
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

            $amount = 1000;
            $result = $this->generatePayGate($user, $amount);
            return response()->json($result);
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

//            $payment = PG::where('authority', $request->authority)->first();
            $payment = $user->payments()->where('authority', $request->authority)->first();
            $status = PG::GetAllStatuses()->where('name', 'پرداخت شده')->first();

            $receipt = Payment::amount(1000)->transactionId($request->authority)->verify();

            // You can show payment referenceId to the user.
            $transactionid = $receipt->getReferenceId();

            $payment->transactionid = $transactionid;
            $payment->purchase_date = $receipt->getDate();
            $payment->status_id = $status->id;
            $payment->save();

            $payment->load('person');
            return response()->json(['data' => $payment, 'message' => 'پرداخت شما با موفقیت انجام شد']);

        } catch (InvalidPaymentException $exception) {
            if ($exception->getCode() == 101) {
                $payment?->load('person');
                return response()->json(['message' => $exception->getMessage(), 'data' => $payment ?? null]);
            } elseif ($exception->getCode() == -51) {
                $status = PG::GetAllStatuses()->where('name', 'پرداخت ناموفق')->first();

                $payment->status_id = $status->id;
                $payment->save();
            }

            return response()->json(['message' => $exception->getMessage()], 400);

        }
    }
}
