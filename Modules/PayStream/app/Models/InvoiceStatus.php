<?php

namespace Modules\PayStream\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PayStream\Database\factories\InvoiceStatusFactory;
use Modules\StatusMS\app\Models\Status;

class InvoiceStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'invoice_id',
        'status_id'
    ];

    protected $table = 'invoice_status';


    public $timestamps = false;


    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }


}
