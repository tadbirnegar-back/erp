<?php

namespace Modules\AddressMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\AddressMS\Database\factories\DistrictFactory;

class District extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): DistrictFactory
    {
        //return DistrictFactory::new();
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
