<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\HRMS\Database\factories\ConformationTypeFactory;

class ConfirmationType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;
    protected $appends = ['identify'];

    public function getIdentifyAttribute()
    {
        $optionID = $this->pivot ? $this->pivot->option_id : null;
        $optionType = $this->pivot ? $this->pivot->option_type : null;
        if ($optionType == null) {
            return null;
        }
        $result = $optionType::procedureIdentifier($optionID);
        return $result;
    }
    public function ScriptType(): BelongsToMany
    {
        return $this->belongsToMany(ScriptType::class, 'confirmation_type_script_type')->withPivot('option_id', 'priority','option_type');
    }

}
