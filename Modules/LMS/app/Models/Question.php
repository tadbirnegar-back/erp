<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\AAA\app\Models\User;
use Modules\LMS\Database\factories\QuestionFactory;
use Modules\StatusMS\app\Models\Status;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Question extends Model
{
    use HasFactory, HasRelationships;

    /**
     * The attributes that are mass assignable.
     */


    public $timestamps = false;

    protected $table = 'questions';

    protected $fillable = [
        'id',
        'title',
        'creator_id',
        'difficulty_id',
        'lesson_id',
        'question_type_id',
        'repository_id',
        'status_id',
        'create_date',
        'chapter_id',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function difficulty()
    {
        return $this->belongsTo(Difficulty::class, 'difficulty_id', 'id');
    }

    public function questionType()
    {
        return $this->belongsTo(QuestionType::class, 'question_type_id', 'id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'course_exams', 'question_id', 'exam_id');
    }

    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }

    public function repository()
    {
        return $this->belongsTo(Repository::class, 'repository_id', 'id');
    }

    public function Questionype()
    {
        return $this->belongsTo(QuestionType::class, 'question_type_id', 'id');

    }

    public function chapter()
    {
        return $this->hasManyDeep(Chapter::class, [Lesson::class,],
            ['lesson_id', 'chapter_id'],
            ['id', 'id']
        );


    }

    public function options()
    {
        return $this->hasMany(Option::class, 'question_id', 'id');
    }

    public function answers()
    {
        return $this->hasMany(Answers::class, 'question_id', 'id');
    }


}
