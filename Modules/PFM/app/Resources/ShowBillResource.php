<?php

namespace Modules\PFM\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\PayStream\app\Http\Enums\OrderStatusEnum;
use Modules\PFM\app\Models\Bill;

class ShowBillResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            "bill_id" => $this->bill_id,
            'status' => [
                "name" => ($this->process_status_name == OrderStatusEnum::PROC_EXPIRED || $this -> process_status_name ===  "لغو شده") ? $this->process_status_name : $this ->financial_status_name,
                "class" => ($this->process_status_name == OrderStatusEnum::PROC_EXPIRED || $this -> process_status_name ===  "لغو شده") ? $this->process_status_class_name : $this ->financial_status_class_name,
            ],
            'person_name' => $this->customer_name,
            'levy_name' => $this->levy_name,
            'due_date' => $this->due_date,
            'national_code' => $this->national_code,
            'total_price' => $this->total_price,
            'discount_value' => $this->discount_value,
            'discounted_price' => $this->total_price - ($this->total_price * $this->discount_value / 100),
            'ounit_name' => $this->ounit_name,
            'params' => $this->getParams($this->bill_id),
            'bank'=>[
                'name'=>$this->bank_name,
                'account_number'=>$this->account_number,
            ],
            'create_date'=>$this->create_date,
        ];
    }

    private function getParams($id)
    {
        $query = Bill::query()
            ->join('pfm_bill_tariff', 'pfm_bills.id', '=', 'pfm_bill_tariff.bill_id')
            ->join('pfm_bill_item_properties', 'pfm_bill_item_properties.bill_tariff_id', '=', 'pfm_bill_tariff.id')
            ->select([
                'pfm_bill_item_properties.key',
                'pfm_bill_item_properties.value',
            ])
            ->where('pfm_bills.id', $id)
            ->whereNotNull('pfm_bill_item_properties.value')
            ->where('pfm_bill_item_properties.value', '!=', 'value')
            ->get();

        return $query;
    }

}
