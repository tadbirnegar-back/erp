<?php

namespace Modules\BDM\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BDM\Database\factories\PoolFactory;
use Modules\StatusMS\app\Models\Status;


class Pool extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'bdm_pools';

    protected $fillable = [
        'height',
        'width',
        'length',
        'app_id',
    ];

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
