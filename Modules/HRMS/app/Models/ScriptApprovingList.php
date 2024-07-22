<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\AAA\app\Models\User;
use Modules\HRMS\Database\factories\ScriptApprovingListFactory;
use Modules\StatusMS\app\Models\Status;

class ScriptApprovingList extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = true;
    const CREATED_AT = 'create_date';
    const UPDATED_AT = 'update_date';
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function script(): BelongsTo
    {
        return $this->belongsTo(RecruitmentScript::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }


    public function employee()
    {
        return $this->script()->employee();
    }

    public function person()
    {
        return $this->employee()->person();
    }

    public static function GetAllStatuses()
    {
        return Status::all()->where('model', '=', self::class);
    }

}
