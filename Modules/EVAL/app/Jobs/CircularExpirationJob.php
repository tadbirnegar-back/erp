<?php

namespace Modules\EVAL\app\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\EVAL\app\Models\EvalCircular;
use Modules\EVAL\app\Models\EvalCircularStatus;
use Modules\EVAL\app\Http\Traits\CircularTrait;
use Modules\EVAL\app\Models\EvalEvaluation;

class CircularExpirationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, CircularTrait;

    /**
     * Create a new job instance.
     */
    public $courseId;

    public function __construct(int $circular)
    {
        $this->circularId = $circular;

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $circular = EvalCircular::find($this->circularId);
        $date = convertPersianToGregorianBothHaveTimeAndDont($circular->expired_date);
        $date = Carbon::parse($date);
        if ( $date != null && $date->format('Y-m-d') == now()->format('Y-m-d')) {
            EvalCircularStatus::create([
                'eval_circular_id' => $this->circularId,
                'status_id' => $this->expiredCircularStatus()->id,
                'created_at' => now(),
            ]);
            EvalEvaluation::create([
                'eval_circular_id' => $this->circularId,
                'status_id' => $this->expiredCircularStatus()->id,
                'created_at' => now(),
            ]);
        }else {
            \Log::info('not');
        }
    }
}
