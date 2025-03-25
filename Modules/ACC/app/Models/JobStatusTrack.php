<?php

namespace Modules\ACC\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\ACC\Database\factories\JobStatusTrackFactory;
use Modules\StatusMS\app\Models\Status;


class JobStatusTrack extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'unique_id',
        'file_id',
        'status',
        'class_name',
        'created_at',
        'updated_at',
    ];

    public $timestamps = false;
    protected $table = 'job_status_tracks';

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public static function GetAllStatuses()
    {
        return Status::where('model', self::class);
    }
}
