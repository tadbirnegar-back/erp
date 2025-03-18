<?php

namespace Modules\SubMS\app\Http\Services;

class InvoiceService
{
    public static function getInvoice($order)
    {
        $orderInvoices = $order->load('invoices.latestInvoiceStatus');
        return $order->invoices;
    }
}
