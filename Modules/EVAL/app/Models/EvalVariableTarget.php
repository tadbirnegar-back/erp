<?php

namespace Modules\EVAL\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\EVAL\Database\factories\EvalVariableTargetFactory;
use Modules\LMS\app\Models\OucPropertyValue;

class EvalVariableTarget extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = ['id',
        'ouc_property_value_id',
        'eval_circular_variables_id',
        'operation'
    ];

    public $timestamps = false;

    public function oucPropertyValue()
    {
        return $this->belongsTo(OucPropertyValue::class, 'ouc_property_value_id', 'id');
    }

    public function evalCircularVariables()
    {
        return $this->belongsTo(EvalCircularVariable::class, 'eval_circular_variables_id', 'id');
    }


}
