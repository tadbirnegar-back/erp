<?php

namespace Modules\PersonMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\AddressMS\app\Models\Address;
use Modules\PersonMS\Database\factories\LegalFactory;

class Legal extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name'];
    public $timestamps = false;

    protected static function newFactory(): LegalFactory
    {
        //return LegalFactory::new();
    }

    public function person()
    {
        return $this->morphOne(Person::class,'personable');
    }

    public function address()
    {
        return $this->belongsTo(Address::class,'address_id');
    }
}
