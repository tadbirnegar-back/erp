<?php

namespace Modules\PayStream\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PayStream\Database\factories\PsPaymentStatusFactory;
use Modules\StatusMS\app\Models\Status;

class PsPaymentStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'status_ps_payment';

    protected $fillable = [
        'status_id',
        'payment_id',
        'create_date',
        'creator_id'
    ];

    public $timestamps = false;


    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function psPayment()
    {
        return $this->belongsTo(PsPayments::class);
    }
}
