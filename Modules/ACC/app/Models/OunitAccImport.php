<?php

namespace Modules\ACC\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\ACC\Database\factories\OunitAccImportFactory;
use Modules\StatusMS\app\Models\Status;


class OunitAccImport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'ounit_id',
        'creator_id',
    ];

    public $timestamps = false;
    protected $table = 'ounitAcc_Imports';

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public static function GetAllStatuses()
    {
        return Status::where('model', self::class);
    }
}
