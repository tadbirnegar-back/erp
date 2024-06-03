<?php

namespace Modules\OUnitMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\OUnitMS\Database\factories\TownOfcFactory;

class TownOfc extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;
    protected static function newFactory(): TownOfcFactory
    {
        //return TownOfcFactory::new();
    }

    public function organizationUnit(): MorphOne
    {
        return $this->morphOne(OrganizationUnit::class,'unitable');
    }

    public function districtOfc(): BelongsTo
    {
        return $this->belongsTo(DistrictOfc::class);
    }

    public function parent()
    {
        return $this->districtOfc();
    }

    public function villageOfcs(): HasMany
    {
        return $this->hasMany(VillageOfc::class, 'town_ofc_id');
    }

    public function children()
    {
        return $this->villageOfcs();
    }
}
