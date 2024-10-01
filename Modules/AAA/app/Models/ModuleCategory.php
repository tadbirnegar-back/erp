<?php

namespace Modules\AAA\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\AAA\Database\factories\ModuleCategoryFactory;

class ModuleCategory extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected $table = 'module_categories';

    protected static function newFactory(): ModuleCategoryFactory
    {
        //return ModuleCategoryFactory::new();
    }

    public function permissions(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(Permission::class, Module::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class, 'module_category_id');
    }
}
