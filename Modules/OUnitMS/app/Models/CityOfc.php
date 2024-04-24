<?php

namespace Modules\OUnitMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\OUnitMS\Database\factories\CityOfcFactory;

class CityOfc extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): CityOfcFactory
    {
        //return CityOfcFactory::new();
    }
    public function organizationUnit(): MorphOne
    {
        return $this->morphOne(OrganizationUnit::class,'unitable');
    }

    public function stateOfc(): BelongsTo
    {
        return $this->belongsTo(StateOfc::class);
    }

    public function parent()
    {
        return $this->stateOfc();
    }

    public function districtOfcs(): HasMany
    {
        return $this->hasMany(DistrictOfc::class, 'city_ofc_id');
    }

    public function children()
    {
        return $this->districtOfcs();

    }
}
