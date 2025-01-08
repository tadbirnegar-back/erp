<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Database\factories\QuestionExamFactory;

class QuestionExam extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    protected $table = 'question_exam';

    protected $fillable = [
        'question_id',
        'exam_id',
        'id'
    ];

    public function questions()
    {
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'id');
    }


    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }

    public function options()
    {
        return $this->hasManyThrough(Option::class,
            Question::class,
            'id',
            'question_id',
            'question_id',
            'id'
        );
    }

}
