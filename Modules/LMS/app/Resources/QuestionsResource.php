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
        return [
            'data' => $this->collection->transform(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'creator' => $item->creator ? [
                        'id' => $item->creator->id,
                        'name' => $item->creator->name,
                    ] : null,
                    'difficulty' => $item->difficulty ? [
                        'id' => $item->difficulty->id,
                        'name' => $item->difficulty->name,
                    ] : null,
                    'lesson' => $item->lesson ? [
                        'id' => $item->lesson->id,
                        'title' => $item->lesson->title,
                    ] : null,
                    'questionType' => $item->questionType ? [
                        'id' => $item->questionType->id,
                        'name' => $item->questionType->name,
                    ] : null,
                    'status' => $item->status ? [
                        'name' => $item->status->name,
                        'className' => $item->status->class_name,
                    ] : null,
                    'repository' => $item->repository ? [
                        'id' => $item->repository->id,
                        'name' => $item->repository->name,
                    ] : null,

                ];
            }),
        ];
    }
}
