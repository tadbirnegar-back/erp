<?php

namespace Modules\OUnitMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\AddressMS\app\Models\Village;
use Modules\OUnitMS\Database\factories\FreeZoneFactory;

class FreeZone extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = ['id'];

    protected $table = 'free_zones_ofcs';

    public $timestamps = false;

    public function organizationUnit(): MorphOne
    {
        return $this->morphOne(OrganizationUnit::class,'unitable');
    }

    public function villages(): HasMany
    {
        return $this->hasMany(VillageOfc::class , 'free_zone_id' , 'id');
    }
}
