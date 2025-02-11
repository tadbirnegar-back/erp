<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\app\Http\Enums\LessonStatusEnum;
use Modules\LMS\app\Http\GlobalScope\ContentScope;
use Modules\LMS\app\Http\GlobalScope\LessonScope;
use Modules\LMS\app\Http\Traits\LessonTrait;
use Modules\LMS\Database\factories\LessonFactory;
use Modules\StatusMS\app\Models\Status;

class Lesson extends Model
{
    use HasFactory , LessonTrait;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    protected $table = 'lessons';

    protected $fillable = [
        'id',
        'chapter_id',
        'description',
        'title'
    ];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class, 'chapter_id', 'id');
    }

    public function files()
    {
        return $this->hasMany(FileLesson::class, 'lesson_id', 'id');
    }

    public function statuses()
    {
        return $this->belongsToMany(Status::class, 'status_lesson', 'lesson_id', 'status_id');
    }


    public function lastPivotStatus()
    {
        $this->hasMany(StatusLesson::class, 'lesson_id', 'id');
    }

    public function latestStatus()
    {
        return $this->belongsToMany(Status::class, 'status_lesson', 'lesson_id', 'status_id')
            ->orderByDesc('id') // Order by ID in descending order
            ->take(1); // Take the latest record
    }

    public function latestStatusForChapter()
    {
        $status = $this->lessonActiveStatus()->id;
        return $this->hasOneThrough(
            Status::class,
            StatusLesson::class,
            'lesson_id',
            'id',
            'id',
            'status_id'
        )
            ->where('status_lesson.status_id', $status)
            ->whereRaw('status_lesson.id = (SELECT MAX(id) FROM status_lesson WHERE status_lesson.lesson_id = lessons.id)');
    }

    public function latestStatusFirstOne()
    {
        return $this->belongsTo(Status::class, 'status_lesson', 'lesson_id', 'status_id')
            ->orderByDesc('id') // Order by ID in descending order
            ->take(1); // Take the latest record
    }

    public function lastStatus()
    {
        return $this->belongsToMany(Status::class, 'status_lesson')
            ->withPivot('id')
            ->orderBy('status_lesson.id', 'desc')
            ->limit(1); // Get the latest one
    }

    public function contents()
    {
        return $this->hasMany(Content::class, 'lesson_id', 'id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'lesson_id', 'id');
    }


    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }

    public function lessonStatus()
    {
        return $this->hasManyThrough(Status::class, StatusLesson::class, 'lesson_id', 'id', 'id', 'status_id')
            ->latest('status_lesson.id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function lessonStudyLog()
    {
        return $this->hasMany(LessonStudyLog::class, 'lesson_id', 'id');
    }


//    public function lessonStudyLog()
//    {
//        return $this->hasMany(LessonStudyLog::class, 'lesson_id', 'id');
//    }
}
