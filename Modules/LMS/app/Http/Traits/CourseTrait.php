<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Http\Enums\PrivaciesEnum;
use Modules\LMS\app\Models\Course;

trait CourseTrait
{
    public function courseIndex(int $perPage = 10, int $pageNumber = 1, array $data = [])
    {
        $searchTerm = $data['title'] ?? null;

        $query = Course::query()->withCount(['chapters', 'lessons', 'questions'])
            ->with('latestStatus');
        $query->whereHas('latestStatus', function ($query) {
            $query->whereIn('name', ['پایان رسیده', 'در انتظار برگزاری', 'درحال برگزاری', 'پیش نویس ', ' حذف شده', ' قبول',]);
        });

        $query->when($searchTerm, function ($query, $searchTerm) {
            $query->where('courses.title', 'like', '%' . $searchTerm . '%')
                ->whereRaw("MATCH (title) AGAINST (? IN BOOLEAN MODE)", [$searchTerm]);
        });
        return $query->paginate($perPage, ['*'], 'page', $pageNumber);
    }

    public function courseStore(array $data){

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
