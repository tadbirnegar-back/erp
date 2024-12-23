<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Database\factories\ExamFactory;

class Exam extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    protected $table = 'exams';

    protected $fillable = [
        'id',
        'title',
        'repository_id',
        'questions_type_id'
    ];

    public function repository()
    {
        return $this->belongsTo(Repository::class, 'repository_id', 'id');
    }

    public function questionsType()
    {
        return $this->belongsTo(QuestionType::class, 'questions_type_id', 'id');
    }

    public function courseExams()
    {
        return $this->hasMany(CourseExam::class, 'exam_id', 'id');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_exams', 'exam_id', 'course_id');
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'course_exams', 'exam_id', 'question_id');
    }


}
