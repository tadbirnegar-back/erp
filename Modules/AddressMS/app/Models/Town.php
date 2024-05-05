<?php

namespace Modules\AddressMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\AddressMS\Database\factories\TownFactory;

class Town extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): TownFactory
    {
        //return TownFactory::new();
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
}
