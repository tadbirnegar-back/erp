<?php

namespace Modules\LMS\app\Http\Traits;

use Kirschbaum\PowerJoins\PowerJoins;
use Modules\HRMS\app\Models\WorkForce;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\Teacher;
use Modules\PersonMS\app\Http\Traits\PersonTrait;
use Modules\PersonMS\app\Models\Religion;
use Modules\PersonMS\app\Models\ReligionType;

trait TeacherTrait
{
    use PersonTrait;

    public function isPersonTeacher($request)
    {
        $result = $this->naturalPersonExists($request);
        if ($result == null) {
            $message = 'notFound';
            $data = null;
        } elseif ($this->isTeacher($result->id)) {
            $message = 'teacher';
            $data = $result;
        } else {
            $message = 'found';
            $data = [
                'result' => $result,
                'educationalRecords' => $result->workForce?->educationalRecords ?? null
            ];
        }

        $religions = Religion::all();
        $religionType = ReligionType::all();


        return ['data' => $data, 'message' => $message, "religions" => $religions, "religion_type" => $religionType];

    }


    public function isTeacher($personId): bool
    {
        return WorkForce::where('person_id', $personId)
            ->whereMorphedTo('workforceable', Teacher::class)
            ->exists();
    }


    public function storeTeacher($data, $personId)
    {
        $teacher = Teacher::create();
        $teacher->workForce()->create([
            'person_id' => $personId,
            'isMarried' => $data['isMarried'] ?? false,
            'military_service_status_id' => $data['militaryServiceStatus'] ?? null,
            'isar_id' => $data['isar_id'] ?? null,
        ]);
        return $teacher->load('workForce');
    }

    public function CourseIndex(int $perPage = 10, int $pageNumber = 1, array $data = [])
    {
        $searchTerm = $data['title'] ?? null;
        $query = Course::query()->withCount(
            'chapters');
        $query->withCount(['lessons', 'questions'])
            ->with('statuses')
            ->withCount('chapters')->when($searchTerm, function ($query, $searchTerm) {
                $query->where('courses.title', 'like', '%' . $searchTerm . '%');
            });

        return $query->paginate($perPage, ['*'], 'page', $pageNumber);
    }

}
