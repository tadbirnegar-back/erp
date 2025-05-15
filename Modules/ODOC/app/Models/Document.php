<?php

namespace Modules\ODOC\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\ODOC\Database\factories\DocumentFactory;
use Modules\StatusMS\app\Models\Status;


class Document extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'odoc_documents';

    protected $fillable = [
        'component_to_render',
        'data',
        'model',
        'model_id',
        'serial_number',
        'title',
        'created_date',
        'creator_id',
        'version'
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
