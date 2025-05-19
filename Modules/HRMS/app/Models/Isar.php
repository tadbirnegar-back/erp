<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\HRMS\Database\factories\IsarFactory;
use Modules\StatusMS\app\Models\Status;

class Isar extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'isar_status_id',
        'person_id',
        'status_id',
        'approver_id',
        'create_date',
        'approve_date',
    ];

    public $timestamps = false;

    public function isarStatus(): BelongsTo
    {
        return $this->belongsTo(IsarStatus::class, 'isar_status_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function relativeType(): BelongsTo
    {
        return $this->belongsTo(RelativeType::class, 'relative_type_id');
    }

    public static function GetAllStatuses()
    {
        return Status::where('model', self::class);
    }
}
