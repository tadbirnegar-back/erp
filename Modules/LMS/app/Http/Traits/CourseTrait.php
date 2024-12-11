<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Http\Enums\PrivaciesEnum;
use Modules\LMS\app\Models\Course;

trait CourseTrait
{


    public function courseStore(array $data)
    {
        $prePreparedData = $this->courseDataPreparation($data);
        $course = Course::create($prePreparedData[0]);

        return $course;

    }

    public function courseDataPreparation(array $data)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }
        $data = collect($data);

        $data = $data->map(function ($item) {
            return [
                'id' => $item['cID'] ?? null,
                'title' => $item['title'],
                'price' => $item['price'] ?? null,
                'preview_video_id' => $item['previewVideoID'] ?? null,
                'is_required' => $item['isRequired'] ? 1 : 0,
                'expiration_date' => $item['expirationDate'] ?? null,
                'description' => $item['description'] ?? null,
                'creator_id' => $item['creatorID'],
                'created_date' => $item['createDate'] ?? now(),
                'cover_id' => $item['coverID'] ?? null,
                'access_date' => $item['accessDate'] ?? null,
                'privacy_id' => $item['privacyID'] ?? PrivaciesEnum::PRIVATE->value,
            ];
        });

        return $data->toArray();
    }
}
