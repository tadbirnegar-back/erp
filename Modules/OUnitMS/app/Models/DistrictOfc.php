<?php

namespace Modules\OUnitMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\OUnitMS\Database\factories\DistrictOfcFactory;

class DistrictOfc extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): DistrictOfcFactory
    {
        //return DistrictOfcFactory::new();
    }

    public function organizationUnit(): MorphOne
    {
        return $this->morphOne(OrganizationUnit::class,'unitable');
    }

    public function cityOfc(): BelongsTo
    {
        return $this->belongsTo(CityOfc::class);
    }

    public function villageOfcs()
    {
        return $this->hasManyThrough(VillageOfc::class, TownOfc::class);
}

    public function parent()
    {
        return $this->cityOfc();
}

    public function townOfcs(): HasMany
    {
        return $this->hasMany(TownOfc::class, 'district_ofc_id');
    }

    public function children()
    {
        return $this->townOfcs();
    }
}
