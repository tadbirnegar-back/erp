<?php

namespace Modules\ACC\app\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\ACC\Database\factories\DocumentFactory;
use Modules\ACMS\app\Models\FiscalYear;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\VillageOfc;
use Modules\StatusMS\app\Models\Status;

class Document extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'document_number',
        'description',
        'fiscal_year_id',
        'ounit_id',
        'creator_id',
        'document_date',
        'create_date',
    ];

    public $timestamps = false;
    protected $table = 'acc_documents';

    public function DocumentDate(): Attribute
    {
        return Attribute::make(

            set: function ($value) {
                if (is_null($value)) {
                    return null;
                }
                // Convert to Gregorian
                $dateTimeString = convertJalaliPersianCharactersToGregorian($value);

                return $dateTimeString;
            }
        );
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'document_id');
    }

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class, 'accDocument_status', 'document_id', 'status_id');
    }

    public function village()
    {
        return $this->hasOneThrough(VillageOfc::class, OrganizationUnit::class, 'id', 'id', 'ounit_id', 'unitable_id');

    }

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id');
    }

    public function ounit(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class);
    }

    public static function GetAllStatuses()
    {
        return Status::where('model', self::class);
    }
}
