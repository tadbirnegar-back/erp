<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Modules\HRMS\app\Models\Job;
use Modules\HRMS\app\Models\Level;
use Modules\HRMS\app\Models\Position;

class CourseEmployeeFeature extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'course_employees_features'; // Assuming the table name

    protected $fillable = ['id', 'course_target_id', 'propertyble_type', 'propertyble_id'];


    public function courseTarget()
    {
        return $this->belongsTo(CourseTarget::class, 'course_target_id');
    }

    public function propertyble()
    {
        return $this->morphTo();
    }
}
