<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class QuestionsResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request): array
    {
        return parent::toArray($request);
    }
}
