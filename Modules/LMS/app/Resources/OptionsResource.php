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
            'id' => $request->id,
            'isCorrect' => $request->isCorrect,
            'questionID' => $request->questionID,
            'lable' => $request->lable,

        ];


    }


}
