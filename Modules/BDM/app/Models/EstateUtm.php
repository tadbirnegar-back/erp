<?php

namespace Modules\BDM\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BDM\Database\factories\EstateUtmFactory;
use Modules\StatusMS\app\Models\Status;


class EstateUtm extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'bdm_estate_utm';

    protected $fillable = [
        'estate_id',
        'x',
        'y',
        'zone',
        'is_center',
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
