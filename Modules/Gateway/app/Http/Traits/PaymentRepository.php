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


        $calculated = $this->calculatePrice($user);
        $ounits = collect($calculated['ounits']);
        $invoice = (new Invoice)->amount($calculated['total']);


        return Payment::via('zarinpal')->purchase($invoice, function ($driver, $transactionId) use ($user, $ounits) {
            $status = PG::GetAllStatuses()->where('name', 'در انتظار پرداخت')->first();

            $ounits->each(function ($ou) use ($user, $status, $transactionId) {


                $payment = new PG();
                $payment->user_id = $user->id;
                $payment->authority = $transactionId;
                $payment->amount = $ou['price'];
                $payment->status_id = $status->id;
                $payment->organization_unit_id = $ou['ounitID'];
                $payment->save();


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
                'alreadyPayed' => $payedPrice,
            ];
            $totalAmount += $mustPay;
        });

        return [
            'total' => $totalAmount,
            'ounits' => $amountPerUnit,
        ];
    }

    public function userHasDebt(User $user)
    {
        if (!$user->relationLoaded('organizationUnits')) {
            $user->load(['organizationUnits.unitable', 'organizationUnits.payments' => function ($q) {
                $q->whereHas('status', function ($query) {
                    $query->where('name', 'پرداخت شده');
                });
            }]);
        }

        $calculatedPrice = $this->calculatePrice($user);

        //if total = true user has no debts
        $result['hasDebt'] = $calculatedPrice['total'] <= 0;
        $result['alreadyPayed'] = !(collect($calculatedPrice['ounits'])->sum('alreadyPayed') == 0);

        return $result;
    }


}
