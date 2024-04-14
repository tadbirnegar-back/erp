<?php

namespace Modules\OUnitMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\OUnitMS\Database\factories\OrganizationUnitFactory;

class OrganizationUnit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;
    protected static function newFactory(): OrganizationUnitFactory
    {
        //return OrganizationUnitFactory::new();
    }

    public function unitable(): MorphTo
    {
        return $this->morphTo();
    }
}
