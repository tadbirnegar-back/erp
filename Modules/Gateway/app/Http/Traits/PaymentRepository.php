<?php

namespace Modules\Gateway\app\Http\Traits;

use Modules\AAA\app\Models\User;
use Modules\Gateway\app\Models\Payment as PG;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

trait PaymentRepository
{
    public static $prices = [
        1 => 750000,
        2 => 900000,
        3 => 1000000,
        4 => 1200000,
        5 => 1300000,
        6 => 1400000,
    ];

    public function generatePayGate(User $user)
    {


        $degs = $user->organizationUnits->pluck('unitable.degree')->reject(fn($dg) => $dg === null);

        $amount = 0;
        $degs->each(function ($deg) use (&$amount) {
            $deg = (int)$deg;

//            $currentAmount = 0; // Initialize a variable for current increment
            $currentAmount = match ($deg) {
                1 => 400000,
                2 => 450000,
                3 => 500000,
                4 => 600000,
                5 => 650000,
                6 => 700000,
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
                        1 => 400000,
                        2 => 450000,
                        3 => 500000,
                        4 => 600000,
                        5 => 650000,
                        6 => 700000,
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

    public function calculatePrice(User $user)
    {

        $ounitVillages = $user->organizationUnits;

        $totalAmount = 0;
        $amountPerUnit = [];

        $ounitVillages->each(function ($ounitVill) use (&$totalAmount, &$amountPerUnit) {
            $currentPayment = $ounitVill->payments;
            $villDegree = $ounitVill->unitable->degree;
            $priceForOunit = self::$prices[$villDegree] ?? 0;
            if ($currentPayment->isNotEmpty()) {
                $payedPrice = $currentPayment->sum('amount');
            } else {
                $payedPrice = 0;
            }
            $mustPay = $priceForOunit - $payedPrice;
            $amountPerUnit[] = [
                'ounitID' => $ounitVill->id,
                'price' => $mustPay,
            ];
            $totalAmount += $mustPay;
        });

        return [
            'total' => $totalAmount,
            'ounits' => $amountPerUnit,
        ];
    }


}
