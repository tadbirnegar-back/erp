<?php

namespace Modules\HRMS\app\Http\Repositories;

use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\WorkForce;

class EmployeeRepository
{
    protected Employee $employee;
    protected WorkForce $workForce;

    /**
     * @param Employee $employee
     * @param WorkForce $workForce
     */
    public function __construct(Employee $employee, WorkForce $workForce)
    {
        $this->employee = $employee;
        $this->workForce = $workForce;
    }


    public function store(array $data)
    {
        try {
            \DB::beginTransaction();

            $employee = new $this->employee();

            $employee->save();
            /**
             * @var WorkForce $workForce
             * @var Employee $employee
             */
            $workForce = new $this->workForce();
            $workForce->person_id = $data['personID'];
            $workForce->isMarried = $data['isMarried'] == true;
            $workForce->military_service_status_id = $data['militaryStatusID'] ?? null;

            $employee->workForce()->save($workForce);

            $workForceStatus = $this->workForce::GetAllStatuses()->where('name', '=', 'فعال')->first();

            $workForce->statuses()->attach($workForceStatus->id);

            $positionsAsArray = json_decode($data['positions'], true);
            $levelsAsArray = json_decode($data['levels'], true);


            $employee->possitions()->sync($positionsAsArray);
            $employee->levels()->sync($levelsAsArray);
            \DB::commit();
            return $employee;
        } catch (\Exception $e) {
            \DB::rollBack();
            return $e;
        }
    }

    public function show(int $id)
    {
        return $this->employee::with('workForce')->findOrFail($id);
    }


    public function isPersonEmployee(int $personID)
    {
        $workForce = WorkForce::where('person_id', '=', $personID)->where('workforceable_type', '=', $this->employee::class)->first();

        return $workForce;
    }


}
