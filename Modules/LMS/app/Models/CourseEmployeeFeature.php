<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseEmployeeFeature extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'course_employee_features'; // Assuming the table name

    protected $fillable = ['id', 'course_target_id', 'propertyble_type', 'propertyble_id'];

    /**
     * Define the inverse relationship to CourseTarget.
     */
    public function courseTarget()
    {
        return $this->belongsTo(CourseTarget::class, 'course_target_id');
    }
}
