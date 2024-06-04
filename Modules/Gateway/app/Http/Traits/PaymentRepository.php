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


        $degs = $user->organizationUnits->pluck('unitable.degree')->reject(function ($dg) {
            return $dg === null;
        });

        $amount = 0;
        $degs->each(function ($deg) use (&$amount) {
            $deg = (int)$deg;

//            $currentAmount = 0; // Initialize a variable for current increment
            $currentAmount = match ($deg) {
                1 => 780000,
                2 => 890000,
                3 => 1000000,
                4 => 1220000,
                5 => 1320000,
                6 => 1450000,
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
                        1 => 780000,
                        2 => 890000,
                        3 => 1000000,
                        4 => 1220000,
                        5 => 1320000,
                        6 => 1450000,
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
