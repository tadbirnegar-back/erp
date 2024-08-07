<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\HRMS\Database\factories\IsarFactory;

class Isar extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    public function isarStatus(): BelongsTo
    {
        return $this->belongsTo(IsarStatus::class, 'isar_status_id');
    }

    public function relativeType(): BelongsTo
    {
        return $this->belongsTo(RelativeType::class, 'relative_type_id');
    }

}
