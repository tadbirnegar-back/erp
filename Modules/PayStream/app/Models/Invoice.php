<?php

namespace Modules\PayStream\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PayStream\Database\factories\InvoiceFactory;
use Modules\StatusMS\app\Models\Status;

class Invoice extends Model
{
    use HasFactory;


    protected $fillable = [
        'id',
        'creator_id',
        'due_date',
        'create_date',
        'order_id',
        'total_price'
    ];

    protected $table = 'invoices';

    /**
     * The attributes that are mass assignable.
     */


    public $timestamps = false;

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }


    public function statuses()
    {
        return $this->belongsToMany(Status::class, 'invoice_status', 'invoice_id', 'status_id');
    }

    public function status()
    {
        return $this->statuses()
            ->orderBy('status_order.id', 'desc')
            ->limit(1);
    }

    public function statusOrders()
    {
        return $this->hasMany(InvoiceStatus::class);
    }

}
