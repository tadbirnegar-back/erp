<?php

namespace Modules\HRMS\app\Http\Services;

use Modules\HRMS\app\Http\Repositories\EmployeeRepository;

class EmployeeService
{
    protected EmployeeRepository $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }


    public function isPersonEmployee(int $personID)
    {
        return $this->employeeRepository->isPersonEmployee($personID);
    }

    public function store(array $data)
    {
        return $this->store($data);
    }


}
