<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Modules\AAA\app\Models\User;
use Modules\FileMS\app\Models\File;
use Modules\HRMS\Database\factories\RecruitmentScriptFactory;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\VillageOfc;
use Modules\PersonMS\app\Models\Person;
use Modules\StatusMS\app\Models\Status;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Staudenmeir\EloquentHasManyDeep\HasTableAlias;

class RecruitmentScript extends Model
{
    use HasEagerLimit, HasTableAlias;

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

    public static function GetAllStatuses()
    {
        return Status::where('model', '=', self::class);
    }

//    public function latestStatus()
//    {
//
//        return $this->belongsToMany(Status::class, 'recruitment_script_status')
//            ->withPivot('create_date')
//            ->orderBy('create_date', 'desc')
//            ->latest('create_date')->take(1);
//    }

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

    public function getDistrictFromFreeZoneRc()
    {
        return $this->hasManyDeep(OrganizationUnit::class, [
            OrganizationUnit::class . ' as freeZone',
            VillageOfc::class,
            OrganizationUnit::class . ' as village',
            OrganizationUnit::class . ' as townOfc',
        ],
            [
                'id',
                'free_zone_id',
                'unitable_id',
                'id',
                'id',
            ],
            [
                'organization_unit_id',
                'unitable_id',
                'id',
                'parent_id',
                'parent_id',
            ]
        )->withoutGlobalScopes()
            ->where('village.unitable_type', VillageOfc::class)
            ->distinct('unitable_id');
    }

    public function person()
    {
        return $this->hasOneDeep(Person::class, [
            WorkForce::class,
        ],
            [
                'workforceable_id',
                'id',

            ],
            [
                'employee_id',
                'person_id',
            ])->where('work_forces.workforceable_type', Employee::class);
    }

}
