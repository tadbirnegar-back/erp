<?php

namespace Modules\ODOC\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\ODOC\Database\factories\DocumentStatusFactory;
use Modules\StatusMS\app\Models\Status;


class DocumentStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'odoc_document_status';

    protected $fillable = [
        'created_date',
        'id',
        'status_id',
        'odoc_document_id',
        'description',
        'creator_id',
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
