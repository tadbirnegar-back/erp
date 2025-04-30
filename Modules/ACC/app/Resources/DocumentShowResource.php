<?php

namespace Modules\ACC\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->document_description,
            'document_date' => $this?->document_date,
            'documentType' => isset($this->document_type_id) ? [
                'name' => $this->document_type_id->getLabel(),
                'id' => $this->document_type_id->value,
            ] : null,
            'read_only' => $this->read_only,
            'status' => [
                'name' => $this->status_name,
                'class_name' => $this->status_class_name,
            ],
            'fiscalYear' => $this->fiscalYear_name,
            'document_number' => $this->document_number,
            'fiscal_year' => ['id' => $this->fiscal_year_id],
            'ounit' => [
                'head' => $this?->ounitHead?->display_name,
                'abadi_code' => $this->village_abadicode,
                'name' => $this->ounit->name,
                'ancestors' => $this->ounit->ancestors->map(function ($ancestor) {
                    return [
                        'name' => $ancestor->name,
                    ];
                }),
            ],
            'financialManager' => $this?->person?->display_name,
            'articles' => $this->articles->isNotEmpty() ? ArticlesListResource::collection($this->articles) : []

        ];
    }
}
