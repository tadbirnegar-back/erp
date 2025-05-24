<?php

namespace Modules\BDM\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BDM\Database\factories\LicenseDocumentFactory;
use Modules\StatusMS\app\Models\Status;


class LicenseDocument extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'bdm_license_documents';

    protected $fillable = [
        'dossier_id',
        'documentable_id',
        'documentable_type',
        'name',
    ];

    public $timestamps = false;

     public static function getTableName()
     {
            return with(new static)->getTable();
     }

     public function documentable()
     {
         return $this->morphTo();
     }

    public static function GetAllStatuses()
    {
        return Status::where('model',self::class);
    }
}
