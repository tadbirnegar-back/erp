<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\AAA\app\Models\User;
use Modules\CustomerMS\app\Models\Customer;
use Modules\FileMS\app\Models\File;
use Modules\LMS\app\Http\Enums\CourseStatusEnum;
use Modules\LMS\app\Http\Enums\LessonStatusEnum;
use Modules\LMS\app\Observers\CourseObserver;
use Modules\LMS\Database\factories\CourseFactory;
use Modules\PayStream\app\Models\Order;
use Modules\PersonMS\app\Models\Person;
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

    protected static function boot()
    {
        parent::boot();

        static::observe(CourseObserver::class);
    }


    public function isRequired(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_null($value)) {
                    return null;
                }
                if ($value == 1) {
                    return true;
                } else {
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
            ->orderByDesc('status_course.id')->take(1);
    }

    public function status()
    {
        return $this->hasOneThrough(Status::class, StatusCourse::class, 'course_id', 'id', 'id', 'status_id')
            ->orderByDesc('status_course.id');
    }

    public function ActiveLesson()
    {
        return $this->hasOneThrough(Status::class, StatusCourse::class, 'course_id', 'id', 'id', 'status_id')
            ->whereNotIn('name', [CourseStatusEnum::DELETED->value])
            ->orderByDesc('status_course.id');
    }

    public function lastStatus()
    {
        return $this->statuses()->orderByDesc('status_course.id')->take(1);
    }

    public function enrolls()
    {
        return $this->hasMany(Enroll::class, 'course_id', 'id');
    }


    public function chapters()
    {
        return $this->hasMany(Chapter::class, 'course_id');
    }

    public function courseExams()
    {
        return $this->hasMany(CourseExam::class, 'course_id', 'id');
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'course_exam', 'course_id', 'exam_id');
    }

    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }

    public function preReqForJoin()
    {
        return $this->hasMany(CourseCourse::class, 'main_course_id', 'id');
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
        return $this->hasManyDeep(LessonStudyLog::class, [Chapter::class, Lesson::class],
            ['course_id', 'chapter_id', 'lesson_id'],
            ['id', 'id', 'id']
        );
    }

    public function lessons()
    {
        return $this->hasManyDeep(Lesson::class, [Chapter::class],
            ['course_id', 'chapter_id'],
            ['id', 'id']
        );
    }

    public function questions()
    {
        return $this->hasManyDeep(Question::class, [Chapter::class, Lesson::class],
            ['course_id', 'chapter_id', 'lesson_id'],
            ['id', 'id', 'id']
        );
    }

    public function person()
    {
        return $this->hasOneDeep(Person::class, [Enroll::class, Order::class, Customer::class],
            ['id', 'id', 'customer_id', 'person_id'],
            ['course_id', 'orderable_id', 'id', 'id']
        );
    }

    public function lastStatusForJoin()
    {
        return $this->hasOne(StatusCourse::class, 'course_id', 'id')->orderBy('id')->take(1);
    }

    public function statusCourse()
    {
        return $this->hasMany(StatusCourse::class, 'course_id', 'id')->orderBy('id')->take(1);
    }

    public function statusCourseDesc()
    {
        return $this->hasMany(StatusCourse::class, 'course_id', 'id')->orderByDesc('id')->take(1);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function courseTarget()
    {
        return $this->hasOne(CourseTarget::class, 'course_id', 'id');
    }

    public function allActiveLessons()
    {
        return $this->lessons()->whereExists(function ($query) {
            $query->select(\DB::raw(1))
                ->from('status_lesson as ls')
                ->join('statuses as s', 'ls.status_id', '=', 's.id')
                ->whereColumn('ls.lesson_id', 'lessons.id')
                ->where('s.name', LessonStatusEnum::ACTIVE->value)
                ->where('ls.created_date', function ($subQuery) {
                    $subQuery->selectRaw('MAX(created_date)')
                        ->from('status_lesson as sub_ls')
                        ->whereColumn('sub_ls.lesson_id', 'ls.lesson_id');
                });
        });
    }

}
