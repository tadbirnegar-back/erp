<?php

namespace Modules\EvalMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\EvalMS\Database\factories\EvaluatorIndicatorFactory;

class EvaluatorIndicator extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): EvaluatorIndicatorFactory
    {
        //return EvaluatorIndicatorFactory::new();
    }

    public function parameters(): HasMany
    {
        return $this->hasMany(EvalParameter::class,'eval_indicator_id');
    }

}
