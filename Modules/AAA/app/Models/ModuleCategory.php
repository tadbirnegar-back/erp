<?php

namespace Modules\AAA\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\AAA\Database\factories\ModuleCategoryFactory;

class ModuleCategory extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected $table = 'module_categories';

    protected static function newFactory(): ModuleCategoryFactory
    {
        //return ModuleCategoryFactory::new();
    }
}
