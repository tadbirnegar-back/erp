<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\HRMS\app\Http\Enums\FormulaEnum;
use Modules\StatusMS\app\Models\Status;
use Staudenmeir\EloquentEagerLimit\Relations\HasMany;

class ScriptAgent extends Model
{

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    //    protected $appends = ['formula'];

    public function getFormulaAttribute()
    {
        $formulaID = $this->pivot ? $this->pivot->formula : null;
        if (is_null($formulaID)) {
            return null;
        }
        return FormulaEnum::from($formulaID)->getPrice();
    }

    public function scriptAgentType(): BelongsTo
    {
        return $this->belongsTo(ScriptAgentType::class, 'script_agent_type_id');
    }

    public function scriptTypes(): BelongsToMany
    {
        return $this->belongsToMany(ScriptType::class, 'script_agent_combos')
            ->using(ScriptAgentCombo::class)
            ->withPivot('id', 'default_value', 'formula', 'hire_type_id');
    }

    public function hireTypes(): BelongsToMany
    {
        return $this->belongsToMany(HireType::class, 'script_agent_combos')->withPivot('default_value', 'formula');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Get all of the combos for the ScriptAgent
     *
     * @return HasMany
     */
    public function combos()
    {
        return $this->hasMany(ScriptAgentCombo::class, 'script_agent_id', 'id');
    }

    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }
}
