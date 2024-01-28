<?php

namespace Modules\PersonMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\AddressMS\app\Models\Address;
use Modules\FileMS\app\Models\File;
use Modules\PersonMS\Database\factories\NaturalFactory;

class Natural extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): NaturalFactory
    {
        //return NaturalFactory::new();
    }

    public function person()
    {
        return $this->morphOne(Person::class, 'personable');
    }

    public function homeAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'home_address_id');
    }

    public function jobAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'job_address_id');
    }
       

}
