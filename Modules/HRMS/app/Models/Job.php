<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\FileMS\app\Models\File;
use Modules\HRMS\Database\factories\JobFactory;
use Modules\StatusMS\app\Models\Status;

class Job extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    public function introVideo(): BelongsTo
    {
        return $this->belongsTo(File::class,'introduction_video_id');
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
