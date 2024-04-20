<?php

namespace Modules\FileMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\FileMS\Database\factories\MimeTypeFactory;

class MimeType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): MimeTypeFactory
    {
        //return MimeTypeFactory::new();
    }

    public function extensions(): HasMany
    {
        return $this->hasMany(Extension::class);
    }

    public function files()
    {
        return $this->hasManyThrough(File::class,Extension::class);
    }
}
