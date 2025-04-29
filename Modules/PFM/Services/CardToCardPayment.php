<?php

namespace Modules\PFM\Services;

use Modules\AAA\app\Models\User;
use Modules\Gateway\app\Models\Payment;
use Modules\PayStream\app\Http\Enums\InvoiceStatusEnum;
use Modules\PayStream\app\Http\Enums\PsPaymentStatusEnum;
use Modules\PayStream\app\Http\Traits\InvoiceTrait;
use Modules\PayStream\app\Http\Traits\OrderTrait;
use Modules\PayStream\app\Http\Traits\PsPaymentTrait;
use Modules\PayStream\app\Models\CardToCards;
use Modules\PayStream\app\Models\Discount;
use Modules\PayStream\app\Models\DiscountInvoice;
use Modules\PayStream\app\Models\FinancialStatus;
use Modules\PayStream\app\Models\Invoice;
use Modules\PayStream\app\Models\InvoiceStatus;
use Modules\PayStream\app\Models\ProcessStatus;
use Modules\PayStream\app\Models\PsPayments;
use Modules\PayStream\app\Models\PsPaymentStatus;

class CardToCardPayment
{
    use PsPaymentTrait , InvoiceTrait , OrderTrait;
    public Invoice $invoice;
    public int $fileID;
    public string $refNumber;
    public User $user;

    public function __construct($invoiceID, $fileID, $refNumber , $user)
    {
        $invoice = Invoice::find($invoiceID);
        $this->invoice = $invoice;
        $this->fileID = $fileID;
        $this->refNumber = $refNumber;
        $this->user = $user;
    }

    public function makeCardToCard()
    {
        $cardToCard = CardToCards::create([
            'receipt_file_id' => $this->fileID,
            'reference_number' => $this->refNumber,
        ]);
        $this->makePayment($cardToCard);
    }


    private function makePayment(CardToCards $cardToCard)
    {
        $invoiceID = $this->invoice->id;
        $discountInvoice = DiscountInvoice::where('invoice_id', $invoiceID)->first();
        $discount = Discount::where('id', $discountInvoice->discount_id)->first();
        $value = $discount->value;
        $discountedPrice = $this->invoice->total_price - ($this->invoice->total_price * $value / 100);
        $payment = PsPayments::create([
            'ps_paymentable_id' => $cardToCard->id,
            'ps_paymentable_type' => CardToCards::class,
            'creator_id' => $this->user->id,
            'create_date' => now(),
            'invoice_id' => $this->invoice->id,
            'payment_date' => now(),
            'total_price' => $discountedPrice,
        ]);

        $this->makePaymentStatus($payment);
        $this->makeInvoiceStatus($this->invoice->id);
        $this->makeOrderStatus($this->invoice->order_id);
    }

    private function makePaymentStatus($payment)
    {

        $status = $this->payedStatus();
        PsPaymentStatus::create([
            'payment_id' => $payment->id,
            'status_id' =>$status->id,
            'creator_id' => $this->user->id,
            'create_date' => now(),
        ]);
    }

    private function makeInvoiceStatus($invoiceID)
    {
        $status = $this->payedInvoiceStatus();
        InvoiceStatus::create([
            'invoice_id' => $invoiceID,
            'status_id' => $status->id,
            'creator_id' => $this->user->id,
            'create_date' => now(),
        ]);
    }

    private function makeOrderStatus($orderID)
    {
        $finStatus = $this->orderFinPardakhtShode();
        $procStatus = $this->orderProcPayed();

        ProcessStatus::create([
            'order_id' => $orderID,
            'creator_id' => $this->user->id,
            'created_date' => now(),
            'status_id' => $procStatus->id,
        ]);

        FinancialStatus::create([
            'order_id' => $orderID,
            'creator_id' => $this->user->id,
            'created_date' => now(),
            'status_id' => $finStatus->id,
        ]);


    }
}
