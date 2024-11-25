<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Modules\AAA\app\Models\User;
use Modules\FileMS\app\Models\File;
use Modules\HRMS\app\Observers\ScriptObserver;
use Modules\HRMS\Database\factories\RecruitmentScriptFactory;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\PersonMS\app\Models\Person;
use Modules\StatusMS\app\Models\Status;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class RecruitmentScript extends Model
{
    use HasEagerLimit;

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'employee_id',
        'organization_unit_id',
        'level_id',
        'position_id',
        'create_date',
        'hire_type_id',
        'job_id',
        'operator_id',
        'script_type_id',
        'start_date',
        'expire_date',
        'parent_id',
        'description',

    ];

    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }

//    public function latestStatus()
//    {
//
//        return $this->belongsToMany(Status::class, 'recruitment_script_status')
//            ->withPivot('create_date')
//            ->orderBy('create_date', 'desc')
//            ->latest('create_date')->take(1);
//    }


    protected static function boot()
    {
        parent::boot();
        // Register the observer
        static::observe(ScriptObserver::class);
    }

    public function status()
    {
        return $this->belongsToMany(Status::class, 'recruitment_script_status');
    }

    public function latestStatus(): HasOneThrough
    {
        return $this->hasOneThrough(
            Status::class,
            RecruitmentScriptStatus::class,
            'recruitment_script_id', // Foreign key on RecruitmentScriptStatus table
            'id', // Foreign key on Status table
            'id', // Local key on RecruitmentScript table
            'status_id' // Local key on RecruitmentScriptStatus table
        )->orderBy('recruitment_script_status.create_date', 'desc');
//            ->latest('recruitment_script_status.create_date');
    }


    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function organizationUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    use \Znck\Eloquent\Traits\BelongsToThrough;

    public function issueTime()
    {
        return $this->belongsToThrough(IssueTime::class, ScriptType::class);
    }

    public function scriptType(): BelongsTo
    {
        return $this->belongsTo(ScriptType::class);
    }

    public function scriptAgents(): BelongsToMany
    {
        return $this->belongsToMany(ScriptAgent::class, 'script_agent_script', 'script_id', 'script_agent_id')
            ->withPivot('contract');
    }

    public function hireType(): BelongsTo
    {
        return $this->belongsTo(HireType::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function approvers()
    {
        return $this->hasMany(ScriptApprovingList::class, 'script_id');
    }

    public function pendingScriptApproving(): HasOne
    {
        return $this->hasOne(ScriptApprovingList::class, 'script_id')->whereHas('status', function ($query) {
            $query->where('name', 'درانتظار تایید من');
        });
    }

    public function files(): BelongsToMany
    {
        return $this->belongsToMany(File::class, 'file_script', foreignPivotKey: 'script_id')->withPivot('title');
    }

    public function ounit(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class, 'organization_unit_id');
    }

    use HasRelationships;

    public function user()
    {
        return $this->hasOneDeep(User::class, [
            WorkForce::class,
            Person::class,
        ],
            [
                'workforceable_id',
                'id',
                'person_id',

            ],
            [
                'employee_id',
                'person_id',
                'id'
            ]);
    }

    public function rejectReason()
    {
        return $this->hasOne(RecruitmentScriptStatus::class, 'recruitment_script_id')->with(['person.avatar', 'person.position', 'status'])->orderBy('create_date', 'desc');
    }

}
