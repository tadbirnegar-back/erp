<?php

namespace Modules\PayStream\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\PayStream\Database\factories\DiscountInvoiceFactory;
use Modules\StatusMS\app\Models\Status;


class DiscountInvoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'discount_invoice';
    protected $fillable = [
        'discount_id',
        'invoice_id',
        'created_date',
    ];

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
