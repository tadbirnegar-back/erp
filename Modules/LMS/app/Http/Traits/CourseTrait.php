<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Models\Course;

trait CourseTrait
{

    public function courseIndex(int $perPage = 10, int $pageNumber = 1, array $data = [])
    {
        $searchTerm = $data['title'] ?? null;

        $query = Course::query()->withCount(['chapters', 'lessons', 'questions'])
            ->with('latestStatus')->whereIn('name', ['پایان رسیده', 'در انتظار برگزاری', 'درحال برگزاری', 'پیش نویس', 'حذف شده', 'قبول', 'دوره به پایان رسیده']);

        $query->when($searchTerm, function ($query, $searchTerm) {
            $query->where('courses.title', 'like', '%' . $searchTerm . '%')
                ->whereRaw("MATCH (title) AGAINST (? IN BOOLEAN MODE)", [$searchTerm]);
        });
        return $query->paginate($perPage, ['*'], 'page', $pageNumber);
    }

}
