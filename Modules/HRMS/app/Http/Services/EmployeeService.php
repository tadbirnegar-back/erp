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

    public function index(int $perPage=10,int $pageNumber=1,array $data=[]){
        return $this->employeeRepository->index($perPage, $pageNumber, $data);
    }

    public function isPersonEmployee(int $personID)
    {
        return $this->employeeRepository->isPersonEmployee($personID);
    }

    public function store(array $data)
    {
        return $this->store($data);
    }

    public function update(array $data,int $id)
    {
        return $this->employeeRepository->update($data, $id);
    }


}
