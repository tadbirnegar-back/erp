<?php

namespace Modules\OUnitMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\OUnitMS\Database\factories\VillageOfcFactory;

class VillageOfc extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['town_ofc_id',
    'degree',
    'hierarchy_code',
    'national_uid',
    'abadi_code',
    'ofc_code',
    'population_1395',
    'household_1395',
    'isTourism',
    'isFarm',
    'isAttached_to_city',
    'hasLicense',
    'license_number',
    'license_date',];
    public $timestamps = false;

    protected static function newFactory(): VillageOfcFactory
    {
        //return VillageOfcFactory::new();
    }

    public function organizationUnit(): MorphOne
    {
        return $this->morphOne(OrganizationUnit::class,'unitable');
    }

    public function townOfc(): BelongsTo
    {
        return $this->belongsTo(TownOfc::class);
    }

    public function parent()
    {
        return $this->townOfc();
    }
}
