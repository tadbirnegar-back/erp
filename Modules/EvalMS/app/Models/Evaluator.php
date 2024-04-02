<?php

namespace Modules\EvalMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\EvalMS\Database\factories\EvaluatorFactory;

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
}
