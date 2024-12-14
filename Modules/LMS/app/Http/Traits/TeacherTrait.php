<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\HRMS\app\Models\WorkForce;
use Modules\LMS\app\Models\Teacher;
use Modules\PersonMS\app\Http\Traits\PersonTrait;

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
            $religion = $result->personable->religion;
            $religion_typee = $result->personable->religionType;
            $levelOfEducation = $result->workForce->load('educationalRecords.levelOfEducation');
            $data = [
                'result' => $result,
                'educationalRecords' => $levelOfEducation ?? null,
                'religion' => $religion,
                'religionType' => $religion_typee,
            ];
        }
        return ['data' => $data, 'message' => $message];

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

    public function teacherIndex(int $perPage = 1, int $pageNumber = 1, array $data = [])
    {

        $searchTerm = $data['display_name'] ?? null;

        $teacherQuery = WorkForce::query();
        $teacherQuery->joinRelationship('person');
        $teacherQuery->where('workforceable_type', Teacher::class);
        $teacherQuery->when($searchTerm, function ($query, $searchTerm) {
            $query->whereRaw("MATCH (persons.display_name) AGAINST (? IN BOOLEAN MODE)", [$searchTerm])
                ->orWhere('persons.display_name', 'like', '%' . $searchTerm . '%');
        });
        $teacherQuery->with(['person:id,display_name']);
        $result = $teacherQuery->paginate($perPage, page: $pageNumber);

        return $result;

    }

    public function teacherLiveSearch($request = [])
    {
        $searchTerm = $request['name'];

        $teacherQuery = WorkForce::query();
//        $teacherQuery->where('workforceable_type', '=', Teacher::class);

        $teacherQuery->joinRelationship('person');

        $teacherQuery->whereRaw("MATCH (persons.display_name) AGAINST (? IN BOOLEAN MODE)", [$searchTerm])
            ->orWhere('persons.display_name', 'like', '%' . $searchTerm . '%')
            ->where('workforceable_type', '=', Teacher::class);
        $teacherQuery->with(['person']);
        return $teacherQuery->get();
    }
}
//        $teacherQuery->joinRelationship('teacher')->where('teachers.id', '=', 'work_forces.workforceable_id');
//        $teacherQuery->joinRelationship('person')->where('persons.id', '=', 'work_forces.person_id');
