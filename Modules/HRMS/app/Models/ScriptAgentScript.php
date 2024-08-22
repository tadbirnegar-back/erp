<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\HRMS\Database\factories\ScriptAgentScriptFactory;

class ScriptAgentScript extends Model
{
    use HasFactory;

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'contract',
        'script_id',
        'script_agent_id',
    ];
    protected $table = 'script_agent_script';
}
