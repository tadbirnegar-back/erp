<?php

namespace Modules\EvalMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\EvalMS\Database\factories\EvaluationFactory;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Evaluation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): EvaluationFactory
    {
        //return EvaluationFactory::new();
    }

    public function parts(): HasMany
    {
        return $this->hasMany(EvalPart::class);
    }

    public function organizationUnits(): BelongsToMany
    {
        return $this->belongsToMany(OrganizationUnit::class);
    }

    use HasRelationships;

    public function parameters(): HasManyDeep
    {
        return $this->hasManyDeep(EvalParameter::class,
            [EvalPart::class, EvaluatorIndicator::class],
            [
                'evaluation_id', // Foreign key on the "eval_parts" table.
                'eval_part_id',    // Foreign key on the "evaluator_indicators" table.
                'eval_indicator_id'     // Foreign key on the "eval_parameters" table.
            ]);
    }

    public function evaluators(): HasMany
    {
        return $this->hasMany(Evaluator::class, 'evaluation_id');
    }
}
