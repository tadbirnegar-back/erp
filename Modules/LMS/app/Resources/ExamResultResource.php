<?php

namespace Modules\LMS\app\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ExamResultResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request): array
    {
        $groupedResults = $this->collection->groupBy('id');

        return [
            'data' => $groupedResults->map(function ($group) {
                return $group->unique('exam_id')->map(function ($item) {

                    $startTime = Carbon::parse($item->start_date_time);
                    $finishTime = Carbon::parse($item->finish_date_time);
                    $usedTimeInMinutes = $startTime->diffInMinutes($finishTime);

                    return [
                        'answer_sheet' => [
                            'start_date_time' => $finishTime->subMinutes($usedTimeInMinutes)->toDateTimeString(),
                            'finish_date_time' => $finishTime->toDateTimeString(),
                            'usedTimeInMinutes' => $usedTimeInMinutes,
                        ],
                    ];
                });
            }),
        ];
    }


}
