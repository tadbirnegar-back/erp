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
            'document_date' => is_null($this->document_date) ? null : convertGregorianToJalali($this->document_date),
            'status' => [
                'name' => $this->latestStatus->name,
                'class_name' => $this->latestStatus->class_name,
            ],
            'fiscalYear' => $this->fiscalYear_name,
            'document_number' => $this->document_number,
            'ounit' => [
                'abadi_code' => $this->village_abadicode,
                'name' => $this->ounit->name,
                'ancestors' => $this->ounit->ancestors->map(function ($ancestor) {
                    return [
                        'name' => $ancestor->name,
                    ];
                }),
            ],
            'articles' => $this->articles->isNotEmpty() ? ArticlesListResource::collection($this->articles) : []

        ];
    }
}
