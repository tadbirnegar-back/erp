<?php

namespace Modules\ProductMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\ProductMS\Database\factories\VariantFactory;
use Modules\StatusMS\app\Models\Status;

class Variant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): VariantFactory
    {
        //return VariantFactory::new();
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function variantGroup(): BelongsTo
    {
        return $this->belongsTo(VariantGroup::class);
    }

    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
