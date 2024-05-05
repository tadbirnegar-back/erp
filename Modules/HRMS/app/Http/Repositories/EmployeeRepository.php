<?php

namespace Modules\HRMS\app\Http\Repositories;

use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\WorkForce;

class EmployeeRepository
{
//    protected Employee $employee;
//    protected WorkForce $workForce;
//
//    /**
//     * @param Employee $employee
//     * @param WorkForce $workForce
//     */
//    public function __construct( WorkForce $workForce)
//    {
////        $this->employee = $employee;
//        $this->workForce = $workForce;
//    }

    public function index(int $perPage = 10, int $pageNumber = 1, array $data = [])
    {
        $employeeQuery = Employee::with('workForce.person.personable', 'workForce.statuses', 'positions');


        $result = $employeeQuery->paginate($perPage, page: $pageNumber);

        return $result;
    }


    public function store(array $data)
    {
        try {
//            \DB::beginTransaction();

            $employee = new Employee();

            $employee->save();
//            /**
//             * @var WorkForce $workForce
//             * @var Employee $employee
//             */
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

//            \DB::commit();
            $employee->load('workForce');
            return $employee;
        } catch (\Exception $e) {
//            \DB::rollBack();
            return $e;
        }
    }

    public function update(array $data, $id)
    {
        try {
//            \DB::beginTransaction();

            $employee = Employee::with('workForce')->findOrFail($id);

            if (is_null($employee)) {
                return null;
            }


//            $employee->save();


            /**
             * @var WorkForce $workForce
             * @var Employee $employee
             */
            $workForce = $employee->workForce;
            $workForce->person_id = $data['personID'];
            $workForce->isMarried = $data['isMarried'] ?1:0;
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
//            \DB::commit();
            return $employee;
        } catch (\Exception $e) {
//            \DB::rollBack();
            return $e;
        }
    }

    public function show(int $id)
    {
        return Employee::with('workForce')->findOrFail($id);
    }


    public function isPersonEmployee(int $personID)
    {
//        $workForce = WorkForce::where('person_id', '=', $personID)->where('workforceable_type', '=', Employee::class)->with('person')->first();
        $employee = Employee::whereHas('workforce', function ($query) use ($personID) {
            $query->where('person_id', '=', $personID);
            // Add additional conditions for workforce here (optional)
        })->with('workforce')->first();
        return $employee;
    }


}
