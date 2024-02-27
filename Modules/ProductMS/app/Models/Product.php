<?php

namespace Modules\ProductMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\AAA\app\Models\User;
use Modules\FileMS\app\Models\File;
use Modules\ProductMS\Database\factories\ProductFactory;
use Modules\StatusMS\app\Models\Status;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): ProductFactory
    {
        //return ProductFactory::new();
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function statuses(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(Variant::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class,'parent_id');
    }

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class,'creator_id');
    }

    public function coverFile(): BelongsTo
    {
        return $this->belongsTo(File::class,'cover_file_id');
    }

    public function porders(): BelongsToMany
    {
        return $this->belongsToMany(Porder::class);
    }
}
