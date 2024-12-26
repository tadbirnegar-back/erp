<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Database\factories\AnswersFactory;

class Answers extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    protected $table = 'answers';

    protected $fillable = [
        'answer_sheet_id',
        'id',
        'question_exam_id',
        'value',
    ];

    public function answerSheet()
    {
        return $this->belongsTo(AnswerSheet::class, 'answer_sheet_id', 'id');
    }

    public function questionExam()
    {
        return $this->belongsTo(QuestionExam::class, 'question_exam_id', 'id');
    }

    public function questions()
    {
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }

    public function options()
    {
        return $this->belongsTo(Option::class, 'value', 'id');
    }

}
