<?php

namespace Modules\CustomerMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\CustomerMS\Database\factories\ShoppingCustomerFactory;

class ShoppingCustomer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): ShoppingCustomerFactory
    {
        //return ShoppingCustomerFactory::new();
    }

    public function customer(): MorphOne
    {
        return $this->morphOne(Customer::class,'customerable');
    }
}
