<?php

namespace Modules\ACC\app\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\AAA\app\Models\User;
use Modules\ACC\app\Http\Enums\DocumentTypeEnum;
use Modules\ACC\Database\factories\DocumentFactory;
use Modules\ACMS\app\Models\FiscalYear;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\VillageOfc;
use Modules\PersonMS\app\Models\Person;
use Modules\StatusMS\app\Models\Status;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Znck\Eloquent\Traits\BelongsToThrough;

class Document extends Model
{
    use HasRelationships, BelongsToThrough;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'document_number',
        'description',
        'fiscal_year_id',
        'document_type_id',
        'ounit_id',
        'creator_id',
        'document_date',
        'create_date',
        'ounit_head_id',
        'read_only',
    ];

    public $timestamps = false;
    protected $table = 'acc_documents';
    protected $casts = [
        'document_type_id' => DocumentTypeEnum::class,
        'read_only' => 'boolean',
    ];

    public function DocumentDate(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_null($value)) {
                    return null;
                }
                // Convert to Jalali
                $dateTimeString = convertGregorianToJalali($value);

                return $dateTimeString;
            },
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

    public function ounitHead()
    {
        return $this->belongsToThrough(Person::class, [User::class],
            foreignKeyLookup: [
                Person::class => 'person_id',
                User::class => 'ounit_head_id',
            ]);
    }

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class, 'accDocument_status', 'document_id', 'status_id');
    }

    public function latestStatus()
    {
        return $this->hasOneThrough(Status::class, DocumentStatus::class, 'document_id', 'id', 'id', 'status_id')
            ->orderBy('accDocument_status.create_date', 'desc');
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


    public function person()
    {
        return $this->belongsToThrough(Person::class, [User::class],
            foreignKeyLookup: [
                Person::class => 'person_id',
                User::class => 'creator_id',
            ]);
    }

    public static function GetAllStatuses()
    {
        return Status::where('model', self::class);
    }
}
