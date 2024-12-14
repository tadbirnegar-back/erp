<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Database\factories\LessonFactory;
use Modules\StatusMS\app\Models\Status;

class Lesson extends Model
{
    use HasFactory;

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

    public function latestStatus()
    {
        return $this->hasOneThrough(Status::class, StatusLesson::class, 'lesson_id', 'id', 'id', 'status_id')
            ->latest('status_lesson.id');
    }

    public function contents()
    {
        return $this->hasMany(Content::class, 'lesson_id', 'id');
    }

    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }


}
