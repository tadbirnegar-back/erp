<?php

namespace Modules\Gateway\app\Http\Traits;

use Modules\AAA\app\Models\User;
use Modules\Gateway\app\Models\Payment as PG;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

trait PaymentRepository
{
    public function generatePayGate(User $user)
    {


        $degs = $user->organizationUnits->pluck('unitable.degree')->reject(fn($dg) => $dg === null);

        $amount = 0;
        $degs->each(function ($deg) use (&$amount) {
            $deg = (int)$deg;

//            $currentAmount = 0; // Initialize a variable for current increment
            $currentAmount = match ($deg) {
                1 => 350000,
                2 => 450000,
                3 => 500000,
                4 => 600000,
                5 => 700000,
                6 => 750000,
                default => 0,
            };

            $amount += $currentAmount;
        });


        $invoice = (new Invoice)->amount($amount);


        return Payment::via('zarinpal')->purchase($invoice, function ($driver, $transactionId) use ($user, $amount, $degs) {
            $status = PG::GetAllStatuses()->where('name', 'در انتظار پرداخت')->first();

            $user->organizationUnits->each(function ($ou) use ($user, $amount, $degs, $status, $transactionId) {
                $deg = $ou->unitable->degree;
                if (!is_null($deg)) {
                    $deg = (int)$deg;

//            $currentAmount = 0; // Initialize a variable for current increment
                    $currentAmount = match ($deg) {
                        1 => 350000,
                        2 => 450000,
                        3 => 500000,
                        4 => 600000,
                        5 => 700000,
                        6 => 750000,
                        default => 0,
                    };

                    $payment = new PG();
                    $payment->user_id = $user->id;
                    $payment->authority = $transactionId;
                    $payment->amount = $currentAmount;
                    $payment->status_id = $status->id;
                    $payment->organization_unit_id = $ou->id;
                    $payment->save();
                }


            });


        })->pay();
    }


}
