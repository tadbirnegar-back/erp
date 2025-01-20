<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\LMS\app\Models\Difficulty;
use Modules\LMS\app\Models\QuestionType;
use Modules\LMS\app\Models\Repository;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $repo = Repository::all();
        $question_type = QuestionType::all();
        $difficulties = Difficulty::all();
        return [
            'course_data' => $this->makeCourseDatas($this->resource),
            'repository' => $repo,
            'questionType' => $question_type,
            'difficulties' => $difficulties
        ];

    }

    private function makeCourseDatas($data)
    {
        return collect($data)
            ->groupBy('chapterID')
            ->map(function ($lessons, $chapterID) {
                return [
                    'chapter_id' => $chapterID,
                    'chapter_title' => $lessons->first()['chapterTitle'],
                    'lessons' => $lessons->map(function ($lesson) {
                        return [
                            'id' => $lesson['lessonID'],
                            'title' => $lesson['lessonTitle'],
                        ];
                    })->values()->unique()
                ];
            })->values();
    }

}
