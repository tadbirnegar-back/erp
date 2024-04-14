<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\HRMS\Database\factories\RecruitmentScriptFactory;
use Modules\StatusMS\app\Models\Status;

class RecruitmentScript extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['employee_id','level_id','organization_unit_id', 'position_id'];
    public $timestamps = false;
    protected static function newFactory(): RecruitmentScriptFactory
    {
        //return RecruitmentScriptFactory::new();
    }
    public function status()
    {
        return $this->belongsToMany(Status::class,'recruitment_script_status');
    }
    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }
}
