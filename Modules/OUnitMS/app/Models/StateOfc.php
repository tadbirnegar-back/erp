<?php

namespace Modules\OUnitMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\OUnitMS\Database\factories\StateOfcFactory;

class StateOfc extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): StateOfcFactory
    {
        //return StateOfcFactory::new();
    }

    public function organizationUnit(): MorphOne
    {
        return $this->morphOne(OrganizationUnit::class,'unitable');
    }

    public function cityOfcs(): HasMany
    {
        return $this->hasMany(CityOfc::class, 'state_ofc_id');
    }

    public function children()
    {
        return $this->cityOfcs();
    }
}
