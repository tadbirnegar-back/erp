<?php

namespace Modules\AAA\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\AAA\Database\factories\ModuleFactory;

class Module extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): ModuleFactory
    {
        //return ModuleFactory::new();
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class, 'module_id');
    }
}
