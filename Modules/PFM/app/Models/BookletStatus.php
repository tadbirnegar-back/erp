<?php

namespace Modules\PFM\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\PFM\Database\factories\BookletStatusFactory;
use Modules\StatusMS\app\Models\Status;


class BookletStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'booklet_id',
        'status_id',
        'created_date',
        'creator_id',
        'description',
        'file_id',
    ];

    protected $table = 'pfm_booklet_statuses';



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
