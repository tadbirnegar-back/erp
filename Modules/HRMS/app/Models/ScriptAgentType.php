<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\HRMS\Database\factories\ScriptAgentTypeFactory;
use Modules\StatusMS\app\Models\Status;

class ScriptAgentType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public static function GetAllStatuses()
    {
        return Status::all()->where('model', '=', self::class);
    }

}
