<?php

namespace Modules\PayStream\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\PayStream\Database\factories\DiscountFactory;
use Modules\StatusMS\app\Models\Status;


class Discount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'max_usage',
        'order_type',
        'expired_date',
        'title',
        'value',
        'value_type',
        'created_date',
    ];

    protected $table = 'discounts';

    public $timestamps = false;

     public static function getTableName()
     {
            return with(new static)->getTable();
     }

    public static function GetAllStatuses()
    {
        return Status::where('model',self::class);
    }
}
