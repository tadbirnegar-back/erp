<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Database\factories\CourseOunitFeaturesFactory;

class CourseOunitFeature extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */


    public $timestamps = false;

    protected $table = 'course_ounit_features';

    protected $fillable = ['id', 'course_target_id', 'ouc_property_value'];

    public function courseTarget()
    {
        return $this->belongsTo(CourseTarget::class, 'course_target_id', 'id');
    }

    public function values()
    {
        return $this->belongsTo(OucPropertyValue::class, 'ouc_property_value', 'id');
    }

}
