<?php

namespace Modules\FormGMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\FormGMS\Database\factories\FieldTypeFactory;

class FieldType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): FieldTypeFactory
    {
        //return FieldTypeFactory::new();
    }
}
