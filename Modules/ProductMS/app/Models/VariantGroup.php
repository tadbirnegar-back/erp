<?php

namespace Modules\ProductMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\ProductMS\Database\factories\VariantGroupFactory;
use Modules\StatusMS\app\Models\Status;

class VariantGroup extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): VariantGroupFactory
    {
        //return VariantGroupFactory::new();
    }

    public function variants(): HasMany
    {
        return $this->hasMany(Variant::class, 'variant_group_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }

}
