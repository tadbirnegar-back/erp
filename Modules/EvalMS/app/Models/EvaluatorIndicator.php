<?php

namespace Modules\EvalMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\EvalMS\Database\factories\EvaluatorIndicatorFactory;

class EvaluatorIndicator extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    
    protected static function newFactory(): EvaluatorIndicatorFactory
    {
        //return EvaluatorIndicatorFactory::new();
    }
}
