<?php

namespace Modules\PFM\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\PFM\Database\factories\TarrifsFactory;
use Modules\StatusMS\app\Models\Status;


class Tarrifs extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'item_id',
        'booklet_id',
        'app_id',
        'value',
        'creator_id',
        'created_date',
    ];

    protected $table = 'pfm_circular_tariffs';

    public $timestamps = false;

     public static function getTableName()
     {
            return with(new static)->getTable();
     }

    public static function GetAllStatuses()
    {
        return Status::where('model',self::class);
    }
}
