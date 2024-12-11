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

}
