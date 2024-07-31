<?php

namespace Modules\HRMS\app\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\AAA\app\Models\User;
use Modules\HRMS\Database\factories\ScriptApprovingListFactory;
use Modules\PersonMS\app\Models\Person;
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
    protected $appends = ['age'];
    protected $casts = [
        'create_date' => 'datetime',
        'update_date' => 'datetime',
    ];
    use \Znck\Eloquent\Traits\BelongsToThrough;

    public function getAgeAttribute()
    {
//        return $this->update_date != null ? Carbon::now()->locale('fa')->diffForHumans($this->update_date) : '-';
        return $this->update_date != null ? $this->update_date->locale('fa')->diffForHumans() : '-';
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
        return $this->belongsToThrough(Employee::class, RecruitmentScript::class, foreignKeyLookup: [
            RecruitmentScript::class => 'script_id',
            Employee::class => 'employee_id',

        ]);
    }


    public function assignedTo()
    {
        return $this->belongsToThrough(Person::class, User::class, foreignKeyLookup: [
            User::class => 'assigned_to',
            Person::class => 'person_id',
        ]);
    }

    public static function GetAllStatuses()
    {
        return Status::all()->where('model', '=', self::class);
    }

}
