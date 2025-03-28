<?php

namespace Modules\ACC\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountUsageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'docNum' => $this->document_number,
            'docDate' => convertGregorianToJalali($this->document_date),
            'docDescription' => $this->doc_description,
            'debtAmount' => $this->debt_amount,
            'creditAmount' => $this->credit_amount,
            'trackingCode' => $this->tracking_code,
            'type' => $this->credit_amount > 0 ? 'بس' : 'بد',
            'remaining' => $this->remaining,

        ];
    }
}
