<?php

namespace Modules\PayStream\app\Http\Traits;

use Modules\PayStream\app\Http\Enums\PsPaymentStatusEnum;
use Modules\PayStream\app\Models\PsPayments;

trait PsPaymentTrait{
    private static string $success = PsPaymentStatusEnum::SUCCESS->value;
    private static string $failed = PsPaymentStatusEnum::FAILED->value;
    private static string $wait_to_pay = PsPaymentStatusEnum::WAITING->value;

    public function payedStatus()
    {
        return PsPayments::GetAllStatuses()->firstWhere('name', $this::$success);
    }

    public function declineStatus()
    {
        return PsPayments::GetAllStatuses()->firstWhere('name', $this::$failed);
    }

    public function waitToPayStatus()
    {
        return PsPayments::GetAllStatuses()->firstWhere('name', $this::$wait_to_pay);
    }

}
