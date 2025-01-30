<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\app\Http\Enums\LessonStatusEnum;
use Modules\LMS\Database\factories\ChapterFactory;
use Modules\StatusMS\app\Models\Status;

class Chapter extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */


    public $timestamps = false;

    protected $table = 'chapters';

    protected $fillable = [
        'id',
        'course_id',
        'description',
        'title',
        'status_id',
        'read_only'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'chapter_id', 'id');
    }


    public function activeLessons()
    {
        return $this->hasMany(Lesson::class, 'chapter_id', 'id')
            ->whereHas('latestStatus', function ($query) {
                $query->where('name', 'Active'); // Filter by 'Active' status
            });
    }
}
