<?php

namespace Modules\OUnitMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphOne;
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
}
