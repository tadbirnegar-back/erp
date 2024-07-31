<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\HRMS\Database\factories\RecruitmentScriptFactory;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\StatusMS\app\Models\Status;

class RecruitmentScript extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['employee_id',
        'organization_unit_id',
        'level_id',
        'position_id',
        'create_date',
        'description',
        'hire_type_id',
        'job_id',
        'operator_id',
        'script_type_id',
        'start_date',
        'expire_date',];
    public $timestamps = false;

    protected static function newFactory(): RecruitmentScriptFactory
    {
        //return RecruitmentScriptFactory::new();
    }

    public function status()
    {
        return $this->belongsToMany(Status::class, 'recruitment_script_status');
    }

    public function latestStatus()
    {

        return $this->belongsToMany(Status::class, 'recruitment_script_status')
            ->withPivot('create_date')
            ->orderBy('create_date', 'desc')
            ->latest('create_date')->take(1);
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

    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }

    use \Znck\Eloquent\Traits\BelongsToThrough;

    public function issueTime()
    {
        return $this->belongsToThrough(IssueTime::class,ScriptType::class);
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

}
