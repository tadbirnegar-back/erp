<?php

namespace Modules\EVAL\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\EVAL\Database\factories\EvalEvaluationAnswersFactory;

class EvalEvaluationAnswer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = ['id',
        'eval_circular_variables_id',
        'eval_evaluation_id',
        'value'
    ];

    public $timestamps = false;


    public function evaluation()
    {
        return $this->belongsTo(EvalEvaluation::class, 'eval_evaluation_id', 'id');
    }

    public function evalCircularVariables()
    {
        return $this->belongsTo(EvalCircularVariable::class, 'eval_circular_variables_id', 'id');
    }
}
