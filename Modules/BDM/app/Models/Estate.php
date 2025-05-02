<?php

namespace Modules\BDM\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BDM\Database\factories\EstateFactory;
use Modules\StatusMS\app\Models\Status;


class Estate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'bdm_estates';

    protected $fillable = [
        'ounit_id',
        'ownership_type_id',
        'part',
        'transfer_type_id',
        'postal_code',
        'address',
        'ounit_number',
        'main',
        'minor',
        'deal_number',
        'building_number',
        'dossier_id',
        'app_id',
        'area',
        'created_date',
        'request_date',
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
