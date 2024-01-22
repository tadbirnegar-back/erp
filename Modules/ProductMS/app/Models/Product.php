<?php

namespace Modules\ProductMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\ProductMS\Database\factories\ProductFactory;
use Modules\StatusMS\app\Models\Status;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): ProductFactory
    {
        //return ProductFactory::new();
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class);
    }

    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(Variant::class);
    }
}
