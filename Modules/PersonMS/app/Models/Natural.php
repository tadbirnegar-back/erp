<?php

namespace Modules\PersonMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\PersonMS\Database\factories\NaturalFactory;

class Natural extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): NaturalFactory
    {
        //return NaturalFactory::new();
    }

    public function person()
    {
        return $this->morphOne(Person::class,'personable');
    }
}
