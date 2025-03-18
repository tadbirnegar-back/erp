<?php

namespace Modules\SubMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\PayStream\app\Models\Order;
use Modules\SubMS\Database\factories\SubscriptionFactory;
use Modules\StatusMS\app\Models\Status;


class Subscription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'sub_subscription';
    protected $fillable = [
        'start_date',
        'expire_date',
        'ounit_id',
    ];

    public $timestamps = false;


    public function order()
    {
        return $this->morphOne(Order::class, 'orderable');
    }


    public static function GetAllStatuses()
    {
        return Status::where('model', self::class);
    }
}
