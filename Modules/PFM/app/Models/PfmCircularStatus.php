<?php

namespace Modules\PFM\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\PFM\Database\factories\PfmCircularStatusFactory;
use Modules\StatusMS\app\Models\Status;


class PfmCircularStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'pfm_circular_statuses';

    protected $fillable = [
        'pfm_circular_id',
        'status_id',
        'description',
        'created_date',
        'creator_id',
    ];

    public $timestamps = false;

    public static function getTableName()
    {
        return with(new static)->getTable();
    }



    public static function GetAllStatuses()
    {
        return Status::where('model', self::class);
    }
}
