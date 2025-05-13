<?php

namespace Modules\PFM\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\PFM\Database\factories\BillTariffFactory;
use Modules\StatusMS\app\Models\Status;


class BillTariff extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'pfm_bill_tariff';

    protected $fillable = [
        'bill_id',
        'tariff_id',
        'id',
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
