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

    public function financialStatuses()
    {
        return $this->belongsToMany(Status::class, 'financial_status', 'order_id', 'status_id');
    }

    public function processStatuses()
    {
        return $this->belongsToMany(Status::class, 'process_status', 'order_id', 'status_id');
    }


    public function latestProcessStatus()
    {
        return $this->hasOneThrough(Status::class, ProcessStatus::class, 'order_id', 'id', 'id', 'status_id')
            ->latest('process_status.id');
    }



    public function latestFinancialStatus()
    {
        return $this->hasOneThrough(Status::class, FinancialStatus::class, 'order_id', 'id', 'id', 'status_id')
            ->latest('financial_status.id');
    }

    public function status()
    {
        return $this->statuses()
            ->orderBy('status_order.id', 'desc')
            ->limit(1);
    }

    public function statusOrders()
    {
        return $this->hasMany(FinancialStatus::class);
    }

    public function latestProcessStatuses()
    {
        return $this->hasOne(
            ProcessStatus::class,
            'order_id',
            'id'
        );
    }


    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'order_id', 'id');
    }


    public static function GetAllFinancialStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', FinancialStatus::class);
    }

    public static function GetAllProcessStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', ProcessStatus::class);
    }
}
