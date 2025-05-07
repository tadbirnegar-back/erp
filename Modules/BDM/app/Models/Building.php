<?php

namespace Modules\BDM\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BDM\Database\factories\BuildingFactory;
use Modules\StatusMS\app\Models\Status;


class Building extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'bdm_building';

    protected $fillable = [
        'app_id',
        'floor_type_id',
        'floor_number_id',
        'all_corbelling_area',
        'floor_height',
        'building_area',
        'storage_area',
        'stairs_area',
        'elevator_shaft',
        'parking_area',
        'corbelling_area',
        'duct_area',
        'other_parts_area',
        'is_existed',
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
