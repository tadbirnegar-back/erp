<?php

namespace Modules\FormGMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\FormGMS\Database\factories\OptionFactory;

class Option extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): OptionFactory
    {
        //return OptionFactory::new();
    }
}
