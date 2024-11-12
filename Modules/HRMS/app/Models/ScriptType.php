<?php

namespace Modules\HRMS\app\Models;

use AjCastro\EagerLoadPivotRelations\EagerLoadPivotTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\HRMS\app\Http\Enums\FormulaEnum;
use Modules\HRMS\app\Http\Enums\ScriptTypeOriginEnum;
use Modules\StatusMS\app\Models\Status;

class ScriptType extends Model
{
    use EagerLoadPivotTrait;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;
    protected $appends = ['formula',
        'origin'];
    protected $casts = [
        'isHeadable' => 'boolean',
        'origin_id' => ScriptTypeOriginEnum::class,
    ];

    public function getOriginAttribute()
    {
        return $this->origin_id?->getLabelAndValue();
    }

    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }

    public function issueTime(): BelongsTo
    {
        return $this->belongsTo(IssueTime::class, 'issue_time_id');
    }

    public function employeeStatus(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'employee_status_id');
    }

    public function confirmationTypes(): BelongsToMany
    {
        return $this->belongsToMany(ConfirmationType::class, 'confirmation_type_script_type')->withPivot('id', 'option_id', 'option_type', 'priority')->orderByPivot('priority');
    }

    public function getFormulaAttribute()
    {
        $formulaID = $this->pivot ? $this->pivot->formula : null;
        if (is_null($formulaID)) {
            return null;
        }
        return FormulaEnum::from($formulaID)->getLabelAndValue();

    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }
}
