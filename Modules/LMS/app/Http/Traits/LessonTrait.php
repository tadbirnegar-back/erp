<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Http\Enums\LessonStatusEnum;
use Modules\LMS\app\Models\FileLesson;
use Modules\LMS\app\Models\Lesson;

trait LessonTrait
{
    public function storeLesson($data)
    {
        return Lesson::create([
            'chapter_id' => $data['chapterID'],
            'description' => $data['description'],
            'title' => $data['title'],
        ]);
    }

    public function storeLessonFiles($data)
    {
        $insertData = $this->prepareLessonFilesData($data);

        FileLesson::insert($insertData);
    }

    private function prepareLessonFilesData($data)
    {
        // Decode the JSON data
        $lessonFiles = json_decode($data['lessonFiles'], true);

        // Prepare the data
        return array_map(function ($file) use ($data) {
            return [
                'lesson_id' => $data['lessonID'],
                'file_id' => $file['fileID'],
                'title' => $file['fileTitle'],
            ];
        }, $lessonFiles);
    }
    public function lessonActiveStatus()
    {
        return Lesson::GetAllStatuses()->firstWhere('name', LessonStatusEnum::ACTIVE->value);
    }
    public function lessonInActiveStatus()
    {
        return Lesson::GetAllStatuses()->firstWhere('name', LessonStatusEnum::IN_ACTIVE->value);
    }
}
