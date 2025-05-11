<?php

namespace Modules\BDM\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BDM\Database\factories\PavilionFactory;
use Modules\StatusMS\app\Models\Status;


class Pavilion extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'bdm_pavilion';

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
