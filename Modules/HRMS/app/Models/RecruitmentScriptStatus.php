<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\HRMS\app\Observers\ScriptStatusObserver;
use Modules\HRMS\Database\factories\RecruitmentScriptStatusFactory;

class recruitmentScriptStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;
    protected $table = 'recruitment_script_status';


    protected $fillable = ['recruitment_script_id', 'status_id'];

    protected static function boot()
    {
        parent::boot();
        // Register the observer
        static::observe(ScriptStatusObserver::class);
    }
}
