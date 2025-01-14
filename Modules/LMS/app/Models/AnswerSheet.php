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
        'score',
        'status_id'
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

    public function answers()
    {
        return $this->hasMany(Answers::class, 'answer_sheet_id', 'id');
    }

    public function repository()
    {
        return $this->hasOneThrough(
            Repository::class,
            Exam::class,
            'id',
            'id',
            'exam_id',
            'repository_id'
        );
    }


}
