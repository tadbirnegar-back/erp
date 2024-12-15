<?php

namespace Modules\PayStream\app\Http\Traits;

use Modules\PayStream\App\Http\Enums\OrderStatusEnum;

class OrderTrait {
    private static string $proc_canceled = OrderStatusEnum::PROC_CANCELED->value;
    private static string $proc_registered = OrderStatusEnum::PROC_REGISTERED->value;
    private static string $proc_wait_mali = OrderStatusEnum::PROC_WAITE_MALI->value;


    private static string $fin_wait_pardakht = OrderStatusEnum::FIN_WAIT_PARDAKHT->value;
    private static string $fin_canceled = OrderStatusEnum::FIN_CANCELED->value;
    private static string $fin_pardakhtShode = OrderStatusEnum::FIN_PARDAKHT_SHODE->value;


    public function orderProcCanceled()
    {
        return Course::GetAllStatuses()->firstWhere('name', OrderStatusEnum::PROC_CANCELED->value);
    }

    public function orderProcRegistered()
    {
        return Course::GetAllStatuses()->firstWhere('name', OrderStatusEnum::PROC_REGISTERED->value);
    }

    public function orderProcWaitMali()
    {
        return Course::GetAllStatuses()->firstWhere('name', OrderStatusEnum::PROC_WAITE_MALI->value);
    }



    public function orderFinWaitPardakht()
    {
        return Course::GetAllStatuses()->firstWhere('name', OrderStatusEnum::FIN_WAIT_PARDAKHT->value);
    }

    public function orderFinCanceled()
    {
        return Course::GetAllStatuses()->firstWhere('name', OrderStatusEnum::FIN_CANCELED->value);
    }

    public function orderFinPardakhtShode()
    {
        return Course::GetAllStatuses()->firstWhere('name', OrderStatusEnum::FIN_PARDAKHT_SHODE->value);
    }
}
