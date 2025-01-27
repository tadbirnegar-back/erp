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
                            'answerSheetID' => $item->answerSheetID,
                            'start_date_time' => $item->start_date_time
                                ? convertGregorianToJalali($item->start_date_time)
                                : 'تاریخ نامشخص',
                            'finish_date_time' => $item->finish_date_time
                                ? convertGregorianToJalali($item->finish_date_time)
                                : 'تاریخ نامشخص',
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

