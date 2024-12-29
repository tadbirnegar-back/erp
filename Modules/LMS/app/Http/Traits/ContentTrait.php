<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Http\Enums\ContentStatusEnum;
use Modules\LMS\app\Models\Content;

trait ContentTrait
{
    public function storeContent($data)
    {
        $insertData = $this->prepareContentData($data);

        Content::insert($insertData);
    }

    private function prepareContentData($data)
    {
        $contents = json_decode($data['contents'], true);

        return array_map(function ($content) use ($data) {
            return [
                'lesson_id' => $data['lessonID'],
                'content_type_id' => $content['contentTypeID'],
                'file_id' => $content['contentFileID'],
                'name' => $content['contentName'],
                'status_id' => $this->contentActiveStatus()->id,
                'teacher_id' => $content['contentTeacherID'],
            ];
        }, $contents);
    }

    public function contentActiveStatus()
    {
        return Content::GetAllStatuses()->firstWhere('name', ContentStatusEnum::ACTIVE->value);
    }

    public function contentInActiveStatus()
    {
        return Content::GetAllStatuses()->firstWhere('name', ContentStatusEnum::IN_ACTIVE->value);
    }
}
