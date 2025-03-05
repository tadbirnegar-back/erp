<?php

namespace Modules\EVAL\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\AAA\app\Models\User;
use Modules\EVAL\Database\factories\EvalEvaluationStatusFactory;
use Modules\StatusMS\app\Models\Status;

class EvalEvaluationStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'status_id',
        'creator_id',
        'eval_evaluation_id',
        'description',
        'created_at',
        'updated_at',
        'id',
    ];

    protected $table = 'evalEvaluation_status';

    public $timestamps = false;

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function evalEvaluation()
    {
        return $this->belongsTo(EvalEvaluation::class, 'eval_evaluation_id', 'id');
    }
}
