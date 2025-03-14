<?php

namespace Modules\ACC\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->document_number,
            'description' => $this->document_description,
            'status' => [
                'name' => $this->status_name,
                'class_name' => $this->status_class_name,
            ],
            'humanReadableDate' => is_null($this->document_date) ? DateformatToHumanReadableJalali(convertGregorianToJalali($this->create_date)) : DateformatToHumanReadableJalali($this->document_date),
            'documentType' => isset($this->document_type_id) ? [
                'name' => $this->document_type_id->getLabel(),
                'id' => $this->document_type_id->value,
            ] : null,
            'totalDebtor' => $this->total_debt_amount ?? 0,
            'totalCreditor' => $this->total_credit_amount ?? 0,
            'difference' => (($this->total_credit_amount ?? 0) - ($this->total_debt_amount ?? 0)),
        ];
    }
}
