<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\HRMS\Database\factories\EmployeeFactory;
use Modules\PersonMS\app\Models\Person;
use Modules\StatusMS\app\Models\Status;

class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): EmployeeFactory
    {
        //return EmployeeFactory::new();
    }

    public function workForce(): MorphOne
    {
        return $this->morphOne(WorkForce::class,'workforceable');
    }

    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class,'recruitment_scripts');
    }

    public function levels(): BelongsToMany
    {
        return $this->belongsToMany(Level::class,'recruitment_scripts');
    }

    public function recruitmentScripts(): HasMany
    {
        return $this->hasMany(RecruitmentScript::class, 'employee_id');
    }

    public function person()
    {
        return $this->hasOneThrough(
            Person::class,
            WorkForce::class,
            'workforceable_id', // Foreign key on WorkForce table
            'id',               // Foreign key on Person table
            'id',               // Local key on Employee table
            'person_id'         // Local key on WorkForce table
        );
//            ->where('workforceable_type', Employee::class);
    }

//    public function latestStatus()
//    {
//        return $this->hasOneThrough(
//            Status::class,
//            WorkForce::class,
//            'workforceable_id', // Foreign key on WorkForce table
//            'id',               // Foreign key on Status table
//            'id',               // Local key on Employee table
//            'workforceable_id'  // Local key on WorkForce table
//        )
//            ->where('workforceable_type', Employee::class)
//            ->latest('status_work_force.create_date');
//    }

    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;


    public function status()
    {
        return $this->hasOneDeep(
            Status::class,
            [WorkForce::class, StatusWorkForce::class],
            [
                'workforceable_id', // Foreign key on the `work_forces` table...
                'work_force_id', // Foreign key on the `status_work_force` table...
                'id' // Foreign key on the `statuses` table...
            ],
            [
                'id', // Local key on the `employees` table...
                'id', // Local key on the `work_forces` table...
                'status_id' // Local key on the `status_work_force` table...
            ]
        )
            ->latest('create_date');
    }

    public function latestRecruitmentScript()
    {
        return $this->hasOne(RecruitmentScript::class)->latestOfMany();
    }

    public static function GetAllStatuses()
    {
        return Status::all()->where('model', '=', self::class);
    }
}
