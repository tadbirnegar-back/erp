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
use Modules\EVAL\app\Models\EvalEvaluationStatus;

class CircularExpirationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, CircularTrait;

    /**
     * Create a new job instance.
     */
    public $circularId;

    public function __construct(int $circularId)
    {
        $this->circularId = $circularId;

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $circular = EvalCircular::find($this->circularId);
        $evaluations = EvalEvaluation::where('eval_circular_id',$this->circularId)->get();

        $date = Carbon::parse($circular->expired_date);
        if ( $date != null && $date->format('Y-m-d') == now()->format('Y-m-d')) {
            EvalCircularStatus::create([
                'eval_circular_id' => $this->circularId,
                'status_id' => $this->expiredCircularStatus()->id,
                'created_at' => now(),
            ]);
            foreach ($evaluations as $evaluation) {
                EvalEvaluationStatus::create([
                    'eval_evaluation_id' => $evaluation->id,
                    'status_id' => $this->expiredCircularStatus()->id,
                    'created_at' => now(),
                    'creator_id' => $evaluation->creator_id,
                ]);
            }
        }
    }
}
