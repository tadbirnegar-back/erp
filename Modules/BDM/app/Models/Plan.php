<?php

namespace Modules\BDM\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BDM\Database\factories\PlanFactory;
use Modules\StatusMS\app\Models\Status;


class Plan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'bdm_plans';

    protected $fillable = [
        'file_id',
        'creator_id',
        'created_date',
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
