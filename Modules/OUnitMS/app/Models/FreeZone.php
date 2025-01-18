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


    protected $table = 'freezones_ofcs';

    protected $fillable = [
        'id' ,
        'ounit_id'
    ];

    public $timestamps = false;

    public function organizationUnit(): MorphOne
    {
        return $this->morphOne(OrganizationUnit::class,'unitable');
    }

}
