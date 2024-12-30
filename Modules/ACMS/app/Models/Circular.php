<?php

namespace Modules\ACMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\ACMS\Database\factories\CircularsFactory;
use Modules\FileMS\app\Models\File;
use Modules\StatusMS\app\Models\Status;

class Circular extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'name',
        'file_id',
        'fiscal_year_id',
    ];

    public $timestamps = false;
    protected $table = 'bgt_circulars';

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class, 'bgtCircular_status', 'circular_id', 'status_id');
    }


    public function finalStatus(): HasOne
    {
        return $this->hasOne(CircularStatus::class);
    }

    public function latestStatus()
    {
        return $this->hasOneThrough(Status::class, CircularStatus::class, 'circular_id', 'id', 'id', 'status_id')->orderBy('bgtCircular_status.id', 'desc');
    }

    public function circularSubjects(): BelongsToMany
    {
        return $this->belongsToMany(CircularSubject::class, 'bgt_circular_items', 'circular_id', 'subject_id');
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }


    public static function GetAllStatuses()
    {
        return Status::where('model', '=', self::class);

    }
}
