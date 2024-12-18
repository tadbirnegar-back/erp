<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OptionsResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request): array
    {
        return [
            'data' => $this->collection->transform(function ($item) {
                return [
                    'id' => $item->id,
                    'isCorrect' => $item->isCorrect,
                    'questionID' => $item->questionID,
                    'lable' => $item->lable,

                ];
            }),
        ];
    }


}
