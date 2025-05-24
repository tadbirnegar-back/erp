<?php

namespace Modules\ODOC\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\ODOC\app\Observers\OdocApproversObserver;
use Modules\ODOC\Database\factories\ApproversFactory;
use Modules\StatusMS\app\Models\Status;


class Approvers extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'odoc_approvers';

    protected $fillable = [
        'person_id',
        'status_id',
        'signed_date',
        'token',
        'signature_id',
        'document_id'
    ];

    public $timestamps = false;

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    protected static function boot()
    {
        parent::boot();

        // Register the observer
        static::observe(OdocApproversObserver::class);
    }

    public static function GetAllStatuses()
    {
        return Status::where('model', self::class);
    }
}
