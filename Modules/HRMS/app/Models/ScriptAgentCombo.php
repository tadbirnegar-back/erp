<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Modules\HRMS\Database\factories\ScriptAgentComboFactory;

class ScriptAgentCombo extends Pivot
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;
    protected $table = 'script_agent_combos';

//    public function scriptAgentType()
//    {
//        return $this->belongsTo(ScriptAgentType::class, 'script_agent_type_id');
//    }
    public function hireType()
    {
        return $this->belongsTo(HireType::class, 'hire_type_id');
    }
}
