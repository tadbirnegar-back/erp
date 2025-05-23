<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\HRMS\app\Http\Enums\FormulaEnum;
use Modules\HRMS\Database\factories\HireTypeFactory;
use Modules\StatusMS\app\Models\Status;

class HireType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;
    protected $appends = ['formula'];

    public function contractType(): BelongsTo
    {
        return $this->belongsTo(ContractType::class, 'contract_type_id');
    }

    public function getFormulaAttribute()
    {
        $formulaID = $this->pivot ? $this->pivot->formula : null;
        if (is_null($formulaID)) {
            return null;
        }
        return FormulaEnum::from($formulaID)->getLabelAndValue();

    }

    public function scriptAgents(): BelongsToMany
    {
        return $this->belongsToMany(ScriptAgent::class,'script_agent_combos')
//            ->using(ScriptAgentCombo::class)
            ->withPivot('formula','default_value');
    }


    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }

}

