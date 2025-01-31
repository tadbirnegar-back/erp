<?php

namespace Modules\LMS\app\Http\Services;

use Modules\AAA\app\Models\User;
use Modules\PayStream\app\Http\Traits\InvoiceTrait;
use Modules\PayStream\app\Http\Traits\OrderTrait;
use Modules\PayStream\app\Http\Traits\PsPaymentTrait;
use Modules\PayStream\app\Models\FinancialStatus;
use Modules\PayStream\app\Models\Invoice;
use Modules\PayStream\app\Models\InvoiceStatus;
use Modules\PayStream\app\Models\Online;
use Modules\PayStream\app\Models\Order;
use Modules\PayStream\app\Models\ProcessStatus;
use Modules\PayStream\app\Models\PsPayments;
use Modules\PayStream\app\Models\PsPaymentStatus;
use Shetabit\Payment\Facade\Payment;

class VerificationPayment
{
    use PsPaymentTrait, InvoiceTrait, OrderTrait;

    public Online $online;

    public function __construct($online)
    {
        $this->online = $online;
    }

    public function verifyPayment()
    {

        $online = $this->online;
        $online->load('psPayments');
        $payment = PsPayments::find($online->psPayments[0]->id);
        //Check Payment
        $total = $payment->total_price;
        $receipt = Payment::amount($total)->transactionId($online->authority)->verify();

        $transactionid = $receipt->getReferenceId();

        //Change Payment Status
        PsPaymentStatus::create([
            "status_id" => $this->payedStatus()->id,
            "payment_id" => $payment->id,
            "create_date" => now(),
            "creator_id" => $payment->creator_id,
        ]);

        //Invoice Status
        $invoice = Invoice::find($payment->invoice_id);

        InvoiceStatus::create([
            "invoice_id" => $invoice->id,
            "status_id" => $this->payedInvoiceStatus()->id,
        ]);


        //Order Status

        $order = Order::find($invoice->order_id);


        $ps = ProcessStatus::create([
            "order_id" => $order->id,
            "status_id" => $this->orderProcRegistered()->id,
            "creator_id" => $order->creator_id,
            "created_date" => now(),
        ]);

        FinancialStatus::create([
            "order_id" => $order->id,
            "status_id" => $this->orderFinPardakhtShode()->id,
            "creator_id" => $order->creator_id,
            "created_date" => now(),
        ]);

        $user = User::with('person')->find($order->creator_id);

        $factor = [
            'transactionid' => $transactionid,
            'purchase_date' => $receipt->getDate(),
            'amount' => $total,
            'status' => "پرداخت شده",
            'person' => $user,

        ];

        return ['data' => $factor, 'message' => 'پرداخت شما با موفقیت انجام شد'];

    }


    public function DeclinePayment()
    {

        $online = $this->online;
        $online->load('psPayments');
        $payment = PsPayments::find($online->psPayments[0]->id);


        //Change Payment Status
        PsPaymentStatus::create([
            "status_id" => $this->declineStatus()->id,
            "payment_id" => $payment->id,
            "create_date" => now(),
            "creator_id" => $payment->creator_id,
        ]);

        //Invoice Status
        $invoice = Invoice::find($payment->invoice_id);

        InvoiceStatus::create([
            "invoice_id" => $invoice->id,
            "status_id" => $this->declineInvoiceStatus()->id,
        ]);


        //Order Status

        $order = Order::find($invoice->order_id);


        $ps = ProcessStatus::create([
            "order_id" => $order->id,
            "status_id" => $this->orderProcCanceled()->id,
            "creator_id" => $order->creator_id,
            "created_date" => now(),
        ]);

        FinancialStatus::create([
            "order_id" => $order->id,
            "status_id" => $this->orderFinCanceled()->id,
            "creator_id" => $order->creator_id,
            "created_date" => now(),
        ]);

        $user = User::with('person')->find($order->creator_id);

        $factor = [
            'transactionid' => "ندارد",
            'purchase_date' => "پرداخت نشده",
            'amount' => "0",
            'status' => "پرداخت نشده",
            'person' => $user,

        ];

        return ['data' => $factor, 'message' => 'پرداخت شما با موفقیت انجام شد'];

    }
}
