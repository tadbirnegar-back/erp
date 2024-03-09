<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\AddressMS\app\services\AddressService;
use Modules\HRMS\app\Http\Services\EducationalRecordService;
use Modules\HRMS\app\Http\Services\EmployeeService;
use Modules\HRMS\app\Http\Services\RelativeService;
use Modules\HRMS\app\Http\Services\ResumeService;
use Modules\HRMS\app\Models\Employee;
use Modules\PersonMS\app\Http\Services\PersonService;

class EmployeeController extends Controller
{
    public array $data = [];
    protected EmployeeService $employeeService;
    protected PersonService $personService;
    protected AddressService $addressService;
    protected RelativeService $relativeService;
    protected ResumeService $resumeService;
    protected EducationalRecordService $educationalRecordService;

    /**
     * @param EmployeeService $employeeService
     * @param PersonService $personService
     * @param AddressService $addressService
     * @param RelativeService $relativeService
     * @param ResumeService $resumeService
     * @param EducationalRecordService $educationalRecordService
     */
    public function __construct(EmployeeService $employeeService, PersonService $personService, AddressService $addressService, RelativeService $relativeService, ResumeService $resumeService, EducationalRecordService $educationalRecordService)
    {
        $this->employeeService = $employeeService;
        $this->personService = $personService;
        $this->addressService = $addressService;
        $this->relativeService = $relativeService;
        $this->resumeService = $resumeService;
        $this->educationalRecordService = $educationalRecordService;
    }


    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {

        return response()->json($this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->all();


        $data['userID'] = \Auth::user()->id;

        if ($request->isNewAddress) {
            $address = $this->addressService->store($data);

            if ($address instanceof \Exception) {
                return response()->json(['message' => 'خطا در وارد کردن آدرس'], 500);
            }

            $data['homeAddressID'] = $address->id;

        }
        if (!isset($request->personID)) {
            $personResult = $this->personService->naturalStore($data);

        } else {
            $personResult = $this->personService->naturalUpdate($data, $data['personID']);
        }

        if ($personResult instanceof \Exception) {
            return response()->json(['message' => 'خطا در افزودن شخص'], 500);
        }
        /**
         * @var Employee $employee
         */
        $employee = $this->employeeService->store($data);

        if ($employee instanceof \Exception) {
            return response()->json(['message' => 'خطا در افزودن کارمند'], 500);
        }

        $educationalData = json_decode($data['educations'],true);

        foreach ($educationalData as $datum) {
            $datum['WorkForceID'] = $employee->workForce->id;
            $education = $this->educationalRecordService->store($datum);

            if ($education instanceof \Exception) {
                return response()->json(['message' => 'خطا در افزودن تحصیلات'], 500);
            }
        }

        $relativeData = json_decode($data['relatives'],true);

        foreach ($relativeData as $datum) {
            $datum['WorkForceID'] = $employee->workForce->id;
            $relative = $this->relativeService->store($datum);

            if ($relative instanceof \Exception) {
                return response()->json(['message' => 'خطا در افزودن تحصیلات'], 500);
            }
        }

        $resumesData = json_decode($data['resumes'],true);

        foreach ($resumesData as $datum) {
            $datum['WorkForceID'] = $employee->workForce->id;
            $resume = $this->resumeService->store($datum);
            if ($resume instanceof \Exception) {
                return response()->json(['message' => 'خطا در افزودن تحصیلات'], 500);
            }
        }


        return response()->json($employee);

    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    public function isPersonEmployee(Request $request)
    {
        $result = $this->personService->naturalExists($request->nationalCode);
        if ($result == null) {
            $message = 'notFound';
            $data = null;
        } elseif ($this->employeeService->isPersonEmployee($result->id)) {
            $message = 'employee';
            $data = $result;
        } else {
            $message = 'found';
            $data = $result;
        }

        return response()->json(['data' => $data, 'message' => $message]);

    }
}
