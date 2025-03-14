<?php

namespace Modules\ACMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\ACMS\Database\factories\CircularStatusFactory;
use Modules\StatusMS\app\Models\Status;

class CircularStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'circular_id',
        'status_id',
        'create_date',
    ];

    public $timestamps = false;
    protected $table = 'bgtCircular_status';

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

}
