<?php

namespace Modules\PayStream\app\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PayStream\Database\factories\StatusOrderFactory;
use Modules\StatusMS\app\Models\Status;

class FinancialStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */


    protected $fillable = [
        'status_id',
        'order_id',
        'id',
        'created_date',
        'creator_id'
    ];

    protected $table = 'financial_status';

    public $timestamps = false;

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }

    public function createdDate(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_null($value)) {
                    return null;
                }
                $jalali = convertDateTimeGregorianToJalaliDateTime($value);

                return $jalali;
            }
        );
    }

}
