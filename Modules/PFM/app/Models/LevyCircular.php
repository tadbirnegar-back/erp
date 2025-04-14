<?php

namespace Modules\PFM\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\PFM\Database\factories\LevyCircularFactory;
use Modules\StatusMS\app\Models\Status;


class LevyCircular extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'pfm_levy_circular';

    protected $fillable = ['circular_id', 'levy_id', 'created_date'];

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
