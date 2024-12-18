<?php

namespace Modules\PayStream\app\Http\Traits;

use Modules\LMS\app\Models\Course;
use Modules\PayStream\App\Http\Enums\InvoiceStatusEnum;
use Modules\PayStream\App\Http\Enums\OrderStatusEnum;
use Modules\PayStream\app\Models\Invoice;

trait InvoiceTrait {
    private static string $payed_invoice = InvoiceStatusEnum::PAYED->value;
    private static string $decline_invoice = InvoiceStatusEnum::DECLINED->value;
    private static string $wait_to_pay_invoice = InvoiceStatusEnum::WAITING->value;

    public function payedInvoiceStatus()
    {
        return Invoice::GetAllStatuses()->firstWhere('name', $this::$payed_invoice);
    }

    public function declineInvoiceStatus()
    {
        return Invoice::GetAllStatuses()->firstWhere('name', $this::$decline_invoice);
    }

    public function waitToPayInvoiceStatus()
    {
        return Invoice::GetAllStatuses()->firstWhere('name', $this::$wait_to_pay_invoice);
    }

}
