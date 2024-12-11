<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\HRMS\app\Models\WorkForce;
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

    public function teacherIndex(int $perPage = 1, int $pageNumber = 1, array $data = [])
    {

        $searchTerm = $data['display_name'] ?? null;

        $teacherQuery = WorkForce::query();
        $teacherQuery->joinRelationship('person');
        $teacherQuery->where('workforceable_type', Teacher::class);
        $teacherQuery->when($searchTerm, function ($query, $searchTerm) {
            $query->whereRaw("MATCH (display_name) AGAINST (? IN BOOLEAN MODE)", [$searchTerm]);
            $query->where('person', function ($query) use ($searchTerm) {
                $query->where('person.display_name', 'like', '%' . $searchTerm . '%')
                    ->where('MATCH(person.display_name) AGAINST(?)', [$searchTerm]);
            });
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

        $teacherQuery->joinRelationship('person')->where('persons.id', '=', 'work_forces.person_id');

        $teacherQuery->whereRaw("MATCH (persons.display_name) AGAINST (? IN BOOLEAN MODE)", [$searchTerm])
            ->orWhere('persons.display_name', 'like', '%' . $searchTerm . '%')
            ->where('workforceable_type', '=', Teacher::class);
        $teacherQuery->with(['person:id,display_name']);
        return $teacherQuery->get();
    }
}
//        $teacherQuery->joinRelationship('teacher')->where('teachers.id', '=', 'work_forces.workforceable_id');
//        $teacherQuery->joinRelationship('person')->where('persons.id', '=', 'work_forces.person_id');
