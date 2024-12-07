<?php
namespace Modules\LMS\app\Http\Trait;
use Modules\HRMS\app\Models\WorkForce;
use Modules\LMS\app\Models\Teacher;
trait TeacherTrait
{
    public function storeteacher(array $data)
    {
        $teacher = new Teacher();
        $teacher->save();

        $workForce = new WorkForce();
        $workForce->person_id = $data['personID'];
        $workForce->isMarried = isset($data['isMarried']) && $data['isMarried'] == true ? 1 : 0;
        $workForce->military_service_status_id = $data['militaryStatusID'] ?? null;

        $teacher->workForce()->save($workForce);
//
//        $workForceStatus = $this->activeEmployeeStatus();
//
//        $workForce->statuses()->attach($workForceStatus->id);

        $teacher->load('workForce');
        return $teacher;

    }

    public function isTeacher($personID)
    {
        $teacher = Teacher::with(['workforce.person' => function ($query) use ($personID) {
            $query->where('id', $personID);
        }])->first();
        return $teacher;


//        $teacher = Teacher::whereHas('workforce', function ($query) use ($personID) {
//            $query->where('person_id', '=', $personID);
//        })->with('workforce')->first();
//        return $teacher;
    }
    public function teacherUpdate(array $data, Teacher $teacher)
    {


        $workForce = $teacher->workForce;
        $workForce->person_id = $data['personID'];
        $workForce->isMarried = isset($data['isMarried']) ? 1 : 0;
        $workForce->military_service_status_id = $data['militaryStatusID'] ?? null;

        $teacher->workForce()->save($workForce);

//        $workForceStatus = $this->activeEmployeeStatus();
//
//        $workForce->statuses()->attach($workForceStatus->id);


//        if (isset($data['positions'])) {
//            $positionsAsArray = json_decode($data['positions'], true);
//            $teacher->positions()->sync($positionsAsArray);
//        }
//
//        if (isset($data['levels'])) {
//            $levelsAsArray = json_decode($data['levels'], true);
//            $teacher->levels()->sync($levelsAsArray);
//        }
//        if (isset($data['skills'])) {
//
//            $skills = json_decode($data['skills'], true);
//
//            $workForce->stdSkills()->sync($skills);
//        }

        return $teacher;

    }


}
