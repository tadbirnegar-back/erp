<?php

namespace Modules\BDM\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BDM\Database\factories\EngineerBuildingFactory;
use Modules\StatusMS\app\Models\Status;


class EngineerBuilding extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'bdm_engineers_building';

    protected $fillable = [
        'engineer_id',
        'dossier_id',
        'engineer_type_id'
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
