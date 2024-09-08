<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Modules\FileMS\app\Models\File;
use Modules\HRMS\Database\factories\RecruitmentScriptFactory;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\StatusMS\app\Models\Status;

class RecruitmentScript extends Model
{
    use HasFactory;

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
    ];

    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }

    protected static function newFactory(): RecruitmentScriptFactory
    {
        //return RecruitmentScriptFactory::new();
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
        return $this->belongsToMany(File::class, 'file_script')->withPivot('title');
    }

    public function ounit(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class, 'organization_unit_id');
    }

}
