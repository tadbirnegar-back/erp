<?php

namespace Modules\PersonMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PersonMS\Database\factories\PersonStatusFactory;
use Modules\StatusMS\app\Models\Status;


class PersonStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];
    protected $table = 'person_status';

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
