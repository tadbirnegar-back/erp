<?php

namespace Modules\ProductMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\ProductMS\Database\factories\VariantGroupFactory;

class VariantGroup extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    
    protected static function newFactory(): VariantGroupFactory
    {
        //return VariantGroupFactory::new();
    }
}
