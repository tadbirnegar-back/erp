<?php

namespace Modules\Gateway\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Gateway\app\Models\Payment as PG;
use Shetabit\Payment\Facade\Payment;

class VerifyPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $authority;

    /**
     * Create a new job instance.
     */
    public function __construct(string $authority)
    {
        $this->authority = $authority;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            \DB::beginTransaction();
            $pendingStatus = PG::GetAllStatuses()->where('name', 'در انتظار پرداخت')->first();

            $payments = PG::where('authority', $this->authority)
                ->where('status_id', $pendingStatus->id)
                ->with('organizationUnit.unitable')->get();

            if ($payments->isNotEmpty()) {
                $status = PG::GetAllStatuses()->where('name', 'پرداخت شده')->first();

                $amount = 0;
                $total = $payments->sum('amount');

                $receipt = Payment::amount($total)->transactionId($this->authority)->verify();

                // You can show payment referenceId to the user.
                $transactionid = $receipt->getReferenceId();

                $payments->each(function ($payment) use ($transactionid, $receipt, $status) {
                    $payment->transactionid = $transactionid;
                    $payment->purchase_date = $receipt->getDate();
                    $payment->status_id = $status->id;
                    $payment->save();
                });
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            $this->fail($e);
        }
    }
}
