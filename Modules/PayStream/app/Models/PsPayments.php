<?php

namespace Modules\PayStream\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PayStream\Database\factories\PsPaymentsFactory;
use Modules\StatusMS\app\Models\Status;

class PsPayments extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'id',
        'ps_paymentable_type',
        'ps_paymentable_id',
        'creator_id',
        'create_date',
        'invoice_id',
        'payment_date',
        'total_price'
    ];

    protected $table = 'ps_payments';


    public $timestamps = false;


    public function psPaymentable()
    {
        return $this->morphTo();
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }


    public function statuses()
    {
        return $this->belongsToMany(Status::class, 'status_ps_payment', 'payment_id', 'status_id');
    }

    public function status()
    {
        return $this->statuses()
            ->orderBy('status_ps_payment.id', 'desc')
            ->limit(1);
    }

    public function statusPsPayments()
    {
        return $this->hasMany(PsPaymentStatus::class);
    }


}
