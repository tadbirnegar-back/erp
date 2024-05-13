<?php

namespace Modules\Gateway\app\Http\Traits;

use Modules\AAA\app\Models\User;
use Modules\Gateway\app\Models\Payment as PG;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

trait PaymentRepository
{
    public function generatePayGate(User $user,$amount=1000)
    {
        $invoice = (new Invoice)->amount($amount);


        return Payment::via('zarinpal')->purchase($invoice, function ($driver, $transactionId) use ($user, $amount) {

            $status = PG::GetAllStatuses()->where('name', 'در انتظار پرداخت')->first();


            $payment = new PG();
            $payment->user_id = $user->id;
            $payment->authority = $transactionId;
            $payment->amount = $amount;
            $payment->status_id = $status->id;
            $payment->save();
        })->pay();
}


}
