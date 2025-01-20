<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ExamListResource extends ResourceCollection
{


    protected string $baseUrl;

    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request): array
    {

        return [
            'data' => $this->collection->transform(function ($item) {
                return [

                    'exam' => $item->examTitle ? [
                        'title' => $item->examTitle,
                        'answer_sheet' => [
                            'start_date_time' => convertGregorianToJalali($item->start_date_time),
                            'finish_date_time' => convertGregorianToJalali($item->finish_date_time),
                        ],
                        'repository' => $item->repositoryID ? [
                            'name' => $item->repositoryName,
                        ] : null,
                        'status' => $item->statusID ? [
                            'name' => $item->status->name,
                            'class_name' => $item->status->class_name,
                        ] : null,
                    ] : null,

                ];
            }),
        ];
    }
}

