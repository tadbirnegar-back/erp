<?php

namespace Modules\EvalMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\EvalMS\Database\factories\EvalParameterFactory;

class EvalParameter extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    
    protected static function newFactory(): EvalParameterFactory
    {
        //return EvalParameterFactory::new();
    }
}
