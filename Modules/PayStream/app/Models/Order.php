<?php

namespace Modules\PayStream\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PayStream\Database\factories\OrderFactory;
use Modules\StatusMS\app\Models\Status;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */


    protected $table = 'orders';


    protected $fillable = [
        'create_date',
        'id',
        'creator_id',
        'customer_id',
        'description',
        'orderable_type',
        'orderable_id',
        'requested_invoice_count',
        'total_price',
    ];

    public $timestamps = false;

    public function orderable()
    {
        return $this->morphMany(Order::class, 'orderable');
    }

    public function statuses()
    {
        return $this->belongsToMany(Status::class, 'status_order', 'order_id', 'status_id');
    }

    public function status()
    {
        return $this->statuses()
            ->orderBy('status_order.id', 'desc')
            ->limit(1);
    }

    public function statusOrders()
    {
        return $this->hasMany(StatusOrder::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'order_id', 'id');
    }



}
