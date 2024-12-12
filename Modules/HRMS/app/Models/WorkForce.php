<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\HRMS\Database\factories\WorkForceFactory;
use Modules\LMS\app\Models\Teacher;
use Modules\PersonMS\app\Models\Person;
use Modules\StatusMS\app\Models\Status;

class WorkForce extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['person_id', 'isMarried'];
    public $timestamps = false;

    protected static function newFactory(): WorkForceFactory
    {
        //return WorkForceFactory::new();
    }

    public function skills()
    {
        return $this->hasMany(SkillWorkForce::class)->with('skill');
    }

    public function stdSkills()
    {
        return $this->belongsToMany(Skill::class, 'skill_work_force');
    }

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class, 'status_work_force');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function relatives(): HasMany
    {
        return $this->hasMany(Relative::class);
    }

    public function resumes(): HasMany
    {
        return $this->hasMany(Resume::class);
    }

    public function educationalRecords(): HasMany
    {
        return $this->hasMany(EducationalRecord::class);
    }

    public function militaryStatus(): BelongsTo
    {
        return $this->belongsTo(MilitaryServiceStatus::class);
    }

    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }

    public function workforceable(): MorphTo
    {
        return $this->morphTo();
    }

    public function courseRecords(): HasMany
    {
        return $this->hasMany(CourseRecord::class, 'workforce_id');
    }

    public function militaryService(): HasOne
    {
        return $this->hasOne(MilitaryService::class, 'work_force_id');
    }

    public function isars(): HasMany
    {
        return $this->hasMany(Isar::class, 'work_force_id');
    }

//    public function statusWorkForces()
//    {
//        return $this->hasMany(StatusWorkForce::class);
//    }
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'workforceable_id');
    }

}
