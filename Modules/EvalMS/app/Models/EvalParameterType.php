<?php

namespace Modules\EvalMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\EvalMS\Database\factories\EvalParameterTypeFactory;

class EvalParameterType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): EvalParameterTypeFactory
    {
        //return EvalParameterTypeFactory::new();
    }
}
