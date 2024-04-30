<?php

namespace Modules\EvalMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\EvalMS\Database\factories\EvalParameterFactory;

class EvalParameter extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;
    protected static function newFactory(): EvalParameterFactory
    {
        //return EvalParameterFactory::new();
    }

    public function options(): HasMany
    {
        return $this->hasMany(EvalParameterOption::class);
    }

    public function parameterType(): BelongsTo
    {
        return $this->belongsTo(EvalParameterType::class,'eval_parameter_type_id');
    }

    public function evalParameterAnswers(): HasMany
    {
        return $this->hasMany(EvalParameterAnswer::class, 'eval_parameter_id');
    }

    public function evalIndicator(): BelongsTo
    {
        return $this->belongsTo(EvaluatorIndicator::class,'eval_indicator_id');
    }

    use \Znck\Eloquent\Traits\BelongsToThrough;

    public function evalPart()
    {
        return $this->belongsToThrough(EvalPart::class,EvaluatorIndicator::class,foreignKeyLookup: [
            EvaluatorIndicator::class => 'eval_indicator_id',
            EvalPart::class => 'eval_part_id',

        ]);
    }

    public function evaluator()
    {
        return $this->belongsToThrough(Evaluator::class,EvalParameterAnswer::class,foreignKeyLookup: [
            EvalParameterAnswer::class => 'eval_parameter_id',
            Evaluator::class => 'evaluator_id',

        ]);
    }
}
