<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ExamsResource extends ResourceCollection
{


    protected string $baseUrl;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->baseUrl = url('/'); // Initialize base URL
    }

    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request): array
    {

        return [
            'data' => $this->collection->transform(function ($item) {
                return [
                    'id' => $item->id,
                    'status_id' => $item->status_id,
                    'start_date_time' => $item->start_date_time,
                    'finish_date_time' => $item->finish_date_time,

                    'repository' => $item->repository_id ? [
                        'id' => $item->repository_id,
                        'name' => $item->repository_name,
                    ] : null,
                    'questionsType' => $item->question_type_id ? [
                        'id' => $item->question_type_id,
                    ] : null,
                    'exam' => $item->exam_title ? [
                        'title' => $item->exam_title,
                    ] : null,
                    'status' => $item->status_id ? [
                        'id' => $item->status_id,
                        'name' => $item->status->name,
                        'class_name' => $item->status->class_name,
                    ] : null,

                ];
            }),
        ];
    }
}

