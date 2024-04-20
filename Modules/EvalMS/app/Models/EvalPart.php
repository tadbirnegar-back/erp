<?php

namespace Modules\EvalMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\EvalMS\Database\factories\EvalPartFactory;

class EvalPart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): EvalPartFactory
    {
        //return EvalPartFactory::new();
    }

    public function indicators(): HasMany
    {
        return $this->hasMany(EvaluatorIndicator::class);
    }
}
