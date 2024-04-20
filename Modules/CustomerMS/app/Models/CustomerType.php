<?php

namespace Modules\CustomerMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CustomerMS\Database\factories\CustomerTypeFactory;

class CustomerType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): CustomerTypeFactory
    {
        //return CustomerTypeFactory::new();
    }
}
