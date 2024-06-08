<?php

namespace Modules\HRMS\app\Http\Traits;

use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\WorkForce;

trait EmployeeTrait
{
    public function employeeIndex(int $perPage = 10, int $pageNumber = 1, array $data = [])
    {
        $employeeQuery = Employee::with('workForce.person.personable', 'workForce.statuses', 'positions')->distinct();


        $result = $employeeQuery->paginate($perPage, page: $pageNumber);

        return $result;
    }


    public function employeeStore(array $data)
    {


            $employee = new Employee();

            $employee->save();

            $workForce = new WorkForce();
            $workForce->person_id = $data['personID'];
            $workForce->isMarried = isset($data['isMarried']) && $data['isMarried']===true ? 1 : 0;
            $workForce->military_service_status_id = $data['militaryStatusID'] ?? null;

            $employee->workForce()->save($workForce);

            $workForceStatus = WorkForce::GetAllStatuses()->where('name', '=', 'فعال')->first();

            $workForce->statuses()->attach($workForceStatus->id);

            if (isset($data['positions'])) {
                $positionsAsArray = json_decode($data['positions'], true);
                $employee->possitions()->sync($positionsAsArray);
            }

            if (isset($data['levels'])) {
                $levelsAsArray = json_decode($data['levels'], true);
                $employee->levels()->sync($levelsAsArray);
            }
            if (isset($data['skills'])) {

                $skills = json_decode($data['skills'], true);

                $workForce->skills()->sync($skills);
            }

            $employee->load('workForce');
            return $employee;

    }

    public function employeeUpdate(array $data, Employee $employee)
    {

            $workForce = $employee->workForce;
            $workForce->person_id = $data['personID'];
            $workForce->isMarried = $data['isMarried'] ?1:0;
            $workForce->military_service_status_id = $data['militaryStatusID'] ?? null;

            $employee->workForce()->save($workForce);

            $workForceStatus = $this->activeWorkForceStatus();

            $workForce->statuses()->attach($workForceStatus->id);


            if (isset($data['positions'])) {
                $positionsAsArray = json_decode($data['positions'], true);
                $employee->possitions()->sync($positionsAsArray);
            }

            if (isset($data['levels'])) {
                $levelsAsArray = json_decode($data['levels'], true);
                $employee->levels()->sync($levelsAsArray);
            }
            if (isset($data['skills'])) {

                $skills = json_decode($data['skills'], true);

                $workForce->skills()->sync($skills);
            }

            return $employee;

    }

    public function employeeShow(int $id)
    {
        return Employee::with('workForce')->findOrFail($id);
    }


    public function isEmployee(int $personID)
    {

        $employee = Employee::whereHas('workforce', function ($query) use ($personID) {
            $query->where('person_id', '=', $personID);
        })->with('workforce')->first();
        return $employee;
    }

    public function activeWorkForceStatus()
    {
        return WorkForce::GetAllStatuses()
            ->firstWhere('name', '=', 'فعال');
    }
}
