<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\HRMS\Database\factories\ScriptTypeFactory;

class ScriptType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    public function conformationType(): BelongsToMany
    {
        return $this->belongsToMany(ConformationType::class, 'conformation_type_script_type')->withPivot('option_id', 'priority');
    }
}
