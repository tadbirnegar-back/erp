<?php

namespace Modules\SubMS\app\Http\Services;

use Modules\PayStream\app\Http\Enums\InvoiceStatusEnum;
use Modules\PayStream\app\Http\Enums\OrderStatusEnum;
use Modules\PayStream\app\Models\Order;
use Modules\SubMS\app\Models\Subscription;

class OrderService
{
    private $subscription;
    private $order;
    private $totalPrice;
    private $payedPrice = 0;
    private int $villageCounts;

    public function __construct($subscription, $price, $villageCounts)
    {
        $paymentData = $subscription->each(function ($subscription) {
            return $subscription->load('order');
        });

        $this->order = $paymentData->pluck('order')->flatten();

        $this->totalPrice = $price;

        $this->subscription = $subscription;

        $this->villageCounts = $villageCounts;
    }

    public function checkOrderForSubscription()
    {
        $ordersStatus = $this->checkOrderStatus();
        if (empty($ordersStatus[0])) {
            return ['isTargeted' => true, 'price' => $this->totalPrice * $this->villageCounts];
        };

        $invoices = $ordersStatus->map(function ($order) {
            return InvoiceService::getInvoice($order);
        });


        foreach ($invoices as $invoice)
        {
            $invoice->each(function ($invoiceWithStatus) {
                if($invoiceWithStatus->latestInvoiceStatus->name == InvoiceStatusEnum::PAYED->value) {
                    $this->payedPrice += $invoiceWithStatus->total_price;
                }
            });
        }

        if($this->payedPrice >= $this->totalPrice * $this->villageCounts) {
            return ['isTargeted' => false];
        } else {
            return ['isTargeted' => true, 'price' => ($this->totalPrice * $this->villageCounts) - $this->payedPrice];
        }
    }


    private function checkOrderStatus()
    {
        return $this->order->each(function ($order) {
            return $order->load('latestFinancialStatus');
        });
    }

}
