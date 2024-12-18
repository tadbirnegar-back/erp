<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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

    public function isRequired(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_null($value)) {
                    return null;
                }
                if($value == 1){
                    return true;
                }else{
                    return false;
                }
            }
        );
    }

    public function accessDate(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_null($value)) {
                    return null;
                }
                $jalali = convertDateTimeGregorianToJalaliDateTime($value);

                return $jalali;
            },

            set: function ($value) {
                if (is_null($value)) {
                    return null;
                }
                // Convert to Gregorian
                $dateTimeString = convertDateTimeHaveDashJalaliPersianCharactersToGregorian($value);

                return $dateTimeString;
            }
        );
    }

    public function expirationDate(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_null($value)) {
                    return null;
                }
                $jalali = convertDateTimeGregorianToJalaliDateTime($value);

                return $jalali;
            },

            set: function ($value) {
                if (is_null($value)) {
                    return null;
                }
                // Convert to Gregorian
                $dateTimeString = convertDateTimeHaveDashJalaliPersianCharactersToGregorian($value);

                return $dateTimeString;
            }
        );
    }

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

    public function latestStatus()
    {
        return $this->hasOneThrough(Status::class, StatusCourse::class, 'course_id', 'id', 'id', 'status_id')
            ->latest('status_course.id');
    }

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

    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }

    public function prerequisiteCourses()
    {
        return $this->belongsToMany(
            self::class,
            'course_course',
            'main_course_id',
            'prerequisite_course_id'
        );
    }

    /**
     * Get the courses that depend on this course as a prerequisite.
     */
    public function dependentCourses()
    {
        return $this->belongsToMany(
            self::class,
            'course_course',
            'prerequisite_course_id',
            'main_course_id'
        );
    }
    use HasRelationships;
    public function lessonStudyLog()
    {
        return $this->hasManyDeep(LessonStudyLog::class, [Chapter::class , Lesson::class] ,
            ['course_id' , 'chapter_id' , 'lesson_id'] ,
            ['id' , 'id' , 'id']
        );
    }

    public function lessons()
    {
        return $this -> hasManyDeep(Lesson::class , [Chapter::class] ,
            ['course_id' , 'chapter_id'],
            ['id' , 'id']
        );
    }

    public function questions()
    {
        return $this->hasManyDeep(Question::class, [Chapter::class, Lesson::class],
            ['course_id', 'chapter_id', 'lesson_id'],
            ['id', 'id', 'id']
        );
    }


}
