<?php

namespace Modules\Merchandise\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Merchandise\Database\factories\MerchandiseProductFactory;
use Modules\ProductMS\app\Models\Product;
use Modules\StatusMS\app\Models\Status;

class MerchandiseProduct extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): MerchandiseProductFactory
    {
        //return MerchandiseProductFactory::new();
    }
    public function product(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Product::class,'productable');
    }

    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }
}
