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
        $searchTerm = $data['name'] ?? null;
        $teacherQuery = WorkForce::where('workforceable_type', Teacher::class)
            ->joinRelationship('person.avatar')
            ->when($searchTerm, function ($query) use ($searchTerm) {
                $query->where(function ($subQuery) use ($searchTerm) {
                    $subQuery
                        ->whereRaw('MATCH(persons.display_name) AGAINST(?)', [$searchTerm])
                        ->orWhere('persons.display_name', 'LIKE', '%' . $searchTerm . '%');
                });
            })
            ->addSelect([
                // Workforce table columns
                'work_forces.id',
                'work_forces.workforceable_type',
                'work_forces.workforceable_id',
                'work_forces.isMarried',
                // Person table columns
                'persons.id as person_id',
                'persons.display_name',
                // File table columns
                'files.slug',
                'files.size',
            ]);
        $result = $teacherQuery->paginate($perPage, page: $pageNumber);

        return $result;

    }

    public function teacherLiveSearch($request = [])
    {
        $searchTerm = $request['name'];

        $teacherQuery = WorkForce::where('workforceable_type', Teacher::class)
            ->joinRelationship('person.avatar', function ($q) use ($searchTerm) {

                $q
                    ->whereRaw('MATCH(persons.display_name) AGAINST(?)', [$searchTerm])
                    ->orWhere('persons.display_name', 'LIKE', '%' . $searchTerm . '%');

            })
            ->addSelect([
                // Workforce table columns
                'work_forces.id',
                'work_forces.workforceable_type',
                'work_forces.workforceable_id',
                'work_forces.isMarried',
                // Person table columns
                'persons.id as person_id',
                'persons.display_name',
                // File table columns
                'files.slug',
                'files.size',
            ]);
        return $teacherQuery->get();
    }
}
//        $teacherQuery->joinRelationship('teacher')->where('teachers.id', '=', 'work_forces.workforceable_id');
//        $teacherQuery->joinRelationship('person')->where('persons.id', '=', 'work_forces.person_id');
