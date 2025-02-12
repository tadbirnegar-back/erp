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
            'difficulties' => $difficulties,
        ];
    }

    /**
     * Make course data with chapters and lessons.
     */
    public function makeCourseDatas($course): array
    {
        $course_data = [];

        // Loop through each chapter and extract its lessons
        foreach ($course['chapters'] as $chapter) {
            $course_data[] = [
                'chapter_id' => $chapter['id'] ?? null,
                'chapter_title' => $chapter['title'] ?? null,
                'lessons' => $this->makeLessons($chapter['all_active_lessons']),
            ];
        }

        return $course_data;
    }

    /**
     * Make lessons data.
     */
    public function makeLessons($lessons): array
    {
        $lesson_data = [];
        foreach ($lessons as $lesson) {
            $lesson_data[] = [
                'lesson_id' => $lesson['id'] ?? null,
                'lesson_title' => $lesson['title'] ?? null,
            ];
        }
        return $lesson_data;
    }
}
