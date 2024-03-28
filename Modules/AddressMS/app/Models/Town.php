<?php

namespace Modules\AddressMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\AddressMS\Database\factories\TownFactory;

class Town extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    
    protected static function newFactory(): TownFactory
    {
        //return TownFactory::new();
    }
}
