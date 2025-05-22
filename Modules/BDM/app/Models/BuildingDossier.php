<?php

namespace Modules\BDM\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BDM\Database\factories\BuildingDossierFactory;
use Modules\ODOC\app\Http\Enums\ModuleCodeEnum;
use Modules\StatusMS\app\Models\Status;


class BuildingDossier extends Model
{
    use HasFactory;

    public int $code = 11;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'bdm_building_dossiers';

    protected $fillable = [
        'created_date',
        'id',
        'bdm_type_id',
        'tracking_code'
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
