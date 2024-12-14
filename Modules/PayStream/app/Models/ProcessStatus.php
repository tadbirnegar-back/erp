<?php

namespace Modules\PayStream\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\PayStream\Database\factories\ProcessStatusFactory;
use Modules\StatusMS\app\Models\Status;

class ProcessStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    public $timestamps = false;

    protected $fillable = [
        'status_id',
        'order_id',
        'id',
        'created_date',
        'creator_id'
    ];

    protected $table = 'process_status';


    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }


}
