<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\HRMS\Database\factories\SkillFactory;
use Modules\StatusMS\app\Models\Status;

class Skill extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): SkillFactory
    {
        //return SkillFactory::new();
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function workForces(): BelongsToMany
    {
        return $this->belongsToMany(WorkForce::class,'skill_work_force')->withPivot(['percentage']);
    }

    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }
}
