<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Database\factories\CourseExamFactory;

class CourseExam extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    protected $table = 'course_exam';

    protected $fillable = [
        'id',
        'course_id',
        'exam_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    public function exams()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'id');
    }

}
