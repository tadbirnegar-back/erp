<?php

namespace Modules\FileMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\FileMS\Database\factories\ExtensionFactory;

class Extension extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): ExtensionFactory
    {
        //return ExtensionFactory::new();
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }
}
