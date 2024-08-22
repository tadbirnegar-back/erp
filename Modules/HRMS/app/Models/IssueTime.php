<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\HRMS\Database\factories\IssueTimeFactory;

class IssueTime extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    public function scriptTypes(): HasMany
    {
        return $this->hasMany(ScriptType::class, 'issue_time_id');
    }

    public function recruitmentScripts()
    {
        return $this->hasManyThrough(RecruitmentScript::class, ScriptType::class, 'issue_time_id', 'script_type_id');
    }

}
