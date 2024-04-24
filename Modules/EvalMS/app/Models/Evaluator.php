<?php

namespace Modules\EvalMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\AAA\app\Models\User;
use Modules\EvalMS\Database\factories\EvaluatorFactory;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\PersonMS\app\Models\Person;
use Znck\Eloquent\Traits\BelongsToThrough;

class Evaluator extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['sum','average','evaluation_id','user_id','parent_id'];
    public $timestamps = false;

    protected static function newFactory(): EvaluatorFactory
    {
        //return EvaluatorFactory::new();
    }

    public function evalParameterAnswers(): HasMany
    {
        return $this->hasMany(EvalParameterAnswer::class, 'evaluator_id');
    }

    public function organizationUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class);
    }

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    use BelongsToThrough;

    public function person()
    {
        return $this->belongsToThrough(Person::class, User::class);
    }
}
