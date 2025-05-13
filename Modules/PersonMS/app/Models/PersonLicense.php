<?php

namespace Modules\PersonMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\FileMS\app\Models\File;
use Modules\PersonMS\app\Http\Enums\PersonLicensesEnums;
use Modules\PersonMS\Database\factories\PersonLicenseFactory;
use Modules\StatusMS\app\Models\Status;


class PersonLicense extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'file_id',
        'person_id',
        'license_type',
    ];

    public $timestamps = false;
    protected $casts = [
        'license_type' => PersonLicensesEnums::class,
    ];

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public static function GetAllStatuses()
    {
        return Status::where('model', self::class);
    }
}
