<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\HRMS\Database\factories\WorkForceFactory;
use Modules\StatusMS\app\Models\Status;

class WorkForce extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

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

    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }
}
