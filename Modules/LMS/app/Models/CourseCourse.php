<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Database\factories\CourseCourseFactory;

class CourseCourse extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    protected $table = 'course_course';

    protected $fillable = [
        'id',
        'main_course_id',
        'prerequisite_course_id'
    ];

    public function mainCourse()
    {
        return $this->belongsTo(Course::class, 'main_course_id', 'id');
    }

    public function preReqCourse()
    {
        return $this->belongsTo(Course::class, 'prerequisite_course_id', 'id');
    }
}
