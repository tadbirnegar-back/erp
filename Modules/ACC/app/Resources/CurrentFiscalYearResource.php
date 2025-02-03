<?php

namespace Modules\ACC\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CurrentFiscalYearResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'openingDoc' => is_null($this['openingDoc']) ? null : [
                'id' => $this['openingDoc']?->id,
                'document_date' => $this['openingDoc']?->document_date,
                'person' => $this['openingDoc']?->person->display_name,
                'articles' => ArticlesListResource::collection($this['openingDoc']->articles),
            ],
            'closeTempDoc' => is_null($this['closeTempDoc']) ? null : [
                'id' => $this['closeTempDoc']?->id,
                'document_date' => $this['closeTempDoc']?->document_date,
                'person' => $this['closeTempDoc']?->person->display_name,
                'articles' => ArticlesListResource::collection($this['closeTempDoc']->articles),
            ],
            'closingDoc' => is_null($this['closingDoc']) ? null : [
                'id' => $this['closingDoc']?->id,
                'document_date' => $this['closingDoc']?->document_date,
                'person' => $this['closingDoc']?->person->display_name,
                'articles' => ArticlesListResource::collection($this['closingDoc']->articles),
            ],
            'fiscalYearStart' => $this['fiscalYear']?->start_date,
            'fiscalYearEnd' => $this['fiscalYear']?->finish_date,
            'ounit' => $this['ounit'],
        ];
    }
}
