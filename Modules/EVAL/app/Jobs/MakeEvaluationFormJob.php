<?php

namespace Modules\EVAL\app\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\EVAL\app\Http\Traits\EvaluationTrait;
use Modules\EVAL\app\Models\EvalCircular;
use Modules\EVAL\app\Models\EvalEvaluation;
use Modules\EVAL\app\Models\EvalEvaluationStatus;

class MakeEvaluationFormJob implements ShouldQueue
{
    use Batchable,Dispatchable, InteractsWithQueue, Queueable, SerializesModels , EvaluationTrait;


    public int $ounitID;
    public int $userID;
    public EvalCircular $circular;
    public int $statusID;
    public function __construct($circular, $ounitID , $userID , $statusID)
    {
        $this->ounitID = $ounitID;
        $this->userID = $userID;
        $this->circular = $circular;
        $this->statusID = $this->evaluationWaitToDoneStatus()->id;
    }
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }
        $eval = EvalEvaluation::create([
            'eval_circular_id' => $this->circular->id,
            'target_ounit_id' => $this->ounitID,
            'evaluator_ounit_id' => $this->ounitID,
            'title' => $this->circular->title,
            'description' => $this->circular->description,
            'create_date' => now(),
            'creator_id' => $this->userID,
            'parent_id' => null,
            'is_revised' => false,
        ]);

        EvalEvaluationStatus::create([
            'eval_evaluation_id' => $eval->id,
            'status_id' => $this->statusID,
            'creator_id' => $this->userID,
            'created_at' => now(),
            'updated_at' => now(),
            'description' => null,
        ]);
    }
}
