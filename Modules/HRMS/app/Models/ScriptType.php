<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\HRMS\Database\factories\ScriptTypeFactory;
use Modules\StatusMS\app\Models\Status;

class ScriptType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;


    public function issueTime(): BelongsTo
    {
        return $this->belongsTo(IssueTime::class,'issue_time_id');
    }

    public function employeeStatus(): BelongsTo
    {
        return $this->belongsTo(Status::class,'employee_status_id');
    }

    public function confirmationTypes(): BelongsToMany
    {
        return $this->belongsToMany(ConfirmationType::class, 'confirmation_type_script_type')->withPivot('option_id', 'priority');
    }
}
