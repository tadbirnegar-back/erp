<?php

namespace Modules\EVAL\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\FileMS\app\Models\File;
use Modules\LMS\app\Models\OucProperty;
use Modules\LMS\app\Models\OucPropertyValue;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class EvalCircularVariable extends Model
{
    use HasFactory , HasRelationships;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = ['id',
        'title',
        'eval_circular_indicator_id',
        'weight',
        'description'
    ];

    public $timestamps = false;


    public function evalCircularIndicator()
    {
        return $this->belongsTo(EvalCircularIndicator::class, 'eval_circular_indicator_id', 'id');
    }

    public function evalVariableTargets()
    {
        return $this->hasMany(EvalVariableTarget::class, 'eval_circular_variables_id', 'id');
    }

    public function files()
    {
        return $this->belongsTo(File::class, 'file_id', 'id');
    }

    public function properties()
    {
        return $this->hasManyDeep(OucProperty::class, [EvalVariableTarget::class,OucPropertyValue::class],
            ['eval_circular_variables_id','id' , 'id'],
            ['id','ouc_property_value_id', 'ouc_property_id'],
        );
    }
}
