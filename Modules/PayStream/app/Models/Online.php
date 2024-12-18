<?php

namespace Modules\PayStream\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\app\Observers\OnlineObserver;
use Modules\PayStream\Database\factories\OnlinesFactory;

class Online extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'onlines';

    protected $fillable = ['id', 'receipt_file_id' , 'authority'];

    public $timestamps = false;

    public function psPayments()
    {
        return $this->morphMany(PsPayments::class, 'ps_paymentable');
    }


}
