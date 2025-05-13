<?php

namespace Modules\PFM\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\PFM\Database\factories\PropApplicationFactory;
use Modules\StatusMS\app\Models\Status;


class PropApplication extends Model
{
    use HasFactory;

    protected $table = 'pfm_prop_applications';

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'name',
        'main_prop_type',
        'adjustment_coefficient',
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
