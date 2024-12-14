<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Modules\FileMS\app\Models\File;
use Modules\LMS\Database\factories\CourseFactory;
use Modules\StatusMS\app\Models\Status;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Course extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     */

    public $timestamps = false;

    protected $table = 'courses';

    protected $fillable = [
        'id',
        'title',
        'price',
        'preview_video_id',
        'is_required',
        'expiration_date',
        'description',
        'creator_id',
        'created_date',
        'cover_id',
        'access_date',
        'privacy_id'
    ];

    public function video()
    {
        return $this->belongsTo(File::class, 'preview_video_id', 'id');
    }

    public function cover()
    {
        return $this->belongsTo(File::class, 'cover_id', 'id');
    }

    public function privacy()
    {
        return $this->belongsTo(Privacy::class, 'privacy_id', 'id');
    }

    public function statuses()
    {
        return $this->belongsToMany(Status::class, 'status_course', 'course_id', 'status_id');
    }

    public function latestStatus(): HasOneThrough
    {
        return $this->hasOneThrough(Status::class, Course::class, 'course_id', 'id', 'id', 'status_id')->orderBy('status_course.id', 'desc');
    }

//    public function latestStatus()
//    {
//        return $this->belongsToMany(Status::class, 'status_course', 'course_id', 'status_id')
//            ->latest();
//    }

    public function enrolls()
    {
        return $this->hasMany(Enroll::class, 'course_id', 'id');
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class, 'course_id', 'id');
    }

    public function courseExams()
    {
        return $this->hasMany(CourseExam::class, 'course_id', 'id');
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'course_exams', 'course_id', 'exam_id');
    }

    public function lessons()
    {
        return $this->hasManyDeep(Lesson::class, [Chapter::class],
            ['course_id', 'chapter_id'],
            ['id', 'id']
        );
    }

    use HasRelationships;

    public function questions()
    {
        return $this->hasManyDeep(Question::class, [Chapter::class, Lesson::class],
            ['course_id', 'chapter_id', 'lesson_id'],
            ['id', 'id', 'id']
        );
    }

}
