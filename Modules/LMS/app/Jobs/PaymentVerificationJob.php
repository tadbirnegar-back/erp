<?php

namespace Modules\LMS\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\LMS\App\Http\Services\VerificationPayment;
use Modules\PayStream\App\Http\Enums\PsPaymentStatusEnum;
use Modules\PayStream\app\Http\Traits\PsPaymentTrait;
use Modules\PayStream\app\Models\PsPayments;

class PaymentVerificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels ;

    public PsPayments $payment;

    /**
     * Create a new job instance.
     */
    public function __construct(PsPayments $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $payment = $this -> payment;
        $payment->load('status','psPaymentable');
        if($payment -> status[0] -> name == PsPaymentStatusEnum::WAITING->value)
        {
            try {
                DB::beginTransaction();
                $verify = new VerificationPayment($payment->psPaymentable);
                $verify -> verifyPayment();
                DB::commit();
            }catch (\Exception $exception){
                DB::rollBack();
                DB::beginTransaction();
                $verify = new VerificationPayment($payment->psPaymentable);
                $verify -> DeclinePayment();
                DB::commit();
            }
        }
    }
}
