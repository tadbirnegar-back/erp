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
}
