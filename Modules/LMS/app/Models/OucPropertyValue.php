<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\EVAL\app\Models\EvalVariableTarget;
use Modules\LMS\Database\factories\OucPropertyValueFactory;

class OucPropertyValue extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    protected $table = 'ouc_property_values';

    protected $fillable = ['id', 'value', 'ouc_property_id'];

    public function oucProperty()
    {
        return $this->belongsTo(OucProperty::class, 'ouc_property_id', 'id');
    }

    public function features()
    {
        return $this->hasMany(CourseOunitFeature::class, 'ouc_property_value', 'id');
    }

    public function evalVariableTargets()
    {
        return $this->hasMany(EvalVariableTarget::class, 'eval_circular_variables_id', 'id');
    }


}
