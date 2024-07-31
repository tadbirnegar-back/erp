<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\HRMS\Database\factories\WorkForceFactory;
use Modules\PersonMS\app\Models\Person;
use Modules\StatusMS\app\Models\Status;

class WorkForce extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): WorkForceFactory
    {
        //return WorkForceFactory::new();
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class,'skill_work_force')->withPivot(['percentage']);
    }

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class,'status_work_force');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class,'person_id');
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

    public function isars(): BelongsTo
    {
        return $this->belongsTo(Isar::class,'isar_id');
    }

//    public function statusWorkForces()
//    {
//        return $this->hasMany(StatusWorkForce::class);
//    }
}
