<?php

namespace Modules\EvalMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\EvalMS\Database\factories\EvalParameterAnswerFactory;

class EvalParameterAnswer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): EvalParameterAnswerFactory
    {
        //return EvalParameterAnswerFactory::new();
    }

    public function evalParameter(): BelongsTo
    {
        return $this->belongsTo(EvalParameter::class,'eval_parameter_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(Evaluator::class,'evaluator_id');
    }
}
