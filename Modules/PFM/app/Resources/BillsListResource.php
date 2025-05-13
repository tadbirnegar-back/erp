<?php

namespace Modules\PFM\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\PayStream\app\Http\Enums\OrderStatusEnum;

class BillsListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            "bill_id" => $this->bill_id,
            'status' => [
                "name" => ($this->process_status_name == OrderStatusEnum::PROC_EXPIRED || $this -> process_status_name ==  OrderStatusEnum::PROC_CANCELED) ? $this->process_status_name : $this ->financial_status_name,
                "class" => ($this->process_status_name == OrderStatusEnum::PROC_EXPIRED || $this -> process_status_name ==  OrderStatusEnum::PROC_CANCELED) ? $this->process_status_class_name : $this ->financial_status_class_name,
            ],
            "customer_name" => $this->customer_name,
            "levy_name" => $this->levy_name,
            "create_date" => $this->create_date,
            "national_code" => $this->national_code,
            "total_price" => $this->total_price,
            "discount_value" => $this->discount_value,
            "discounted_price" => $this->total_price - ($this->total_price * $this->discount_value / 100),
            'ounit_id' => $this->ounit_id,
        ];
    }
}
