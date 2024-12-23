<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Database\factories\AnswerSheetFactory;
use Modules\StatusMS\app\Models\Status;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class AnswerSheet extends Model
{
    use HasFactory, HasRelationships;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    protected $table = 'answer_sheets';

    protected $fillable = [
        'id',
        'exam_id',
        'student_id',
        'finish_date_time',
        'start_date_time',
        'score'
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }
    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }


    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    public function answer()
    {
        return $this->hasMany(Answers::class, 'answer_sheet_id', 'id');
    }

    public function repository()
    {
        return $this->hasOneThrough(
            Repository::class, // مدل نهایی
            Exam::class,       // مدل واسطه
            'id',              // کلید اصلی جدول Exams
            'id',              // کلید اصلی جدول Repositories
            'exam_id',         // کلید خارجی جدول AnswerSheets که به جدول Exams اشاره می‌کند
            'repository_id'    // کلید خارجی جدول Exams که به جدول Repositories اشاره می‌کند
        );
    }

    public function questionType()
    {
        return $this->hasOneThrough(
            QuestionType::class,
            Exam::class,
            'id',
            'id',
            'exam_id',
            'questions_type_id'
        );

    }

    public function questionExam()
    {
        return $this->hasMany(QuestionExam::class, 'exam_id', 'exam_id');
    }

}
