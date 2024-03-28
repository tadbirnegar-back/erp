<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\AddressMS\app\Models\Address;
use Modules\AddressMS\app\Repositories\AddressRepository;
use Modules\AddressMS\app\services\AddressService;
use Modules\HRMS\app\Http\Repositories\EducationalRecordRepository;
use Modules\HRMS\app\Http\Repositories\EmployeeRepository;
use Modules\HRMS\app\Http\Repositories\RelativeRepository;
use Modules\HRMS\app\Http\Repositories\ResumeRepository;
use Modules\HRMS\app\Http\Services\EducationalRecordService;
use Modules\HRMS\app\Http\Services\EmployeeService;
use Modules\HRMS\app\Http\Services\RelativeService;
use Modules\HRMS\app\Http\Services\ResumeService;
use Modules\HRMS\app\Models\EducationalRecord;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\Level;
use Modules\HRMS\app\Models\LevelOfEducation;
use Modules\HRMS\app\Models\MilitaryServiceStatus;
use Modules\HRMS\app\Models\Position;
use Modules\HRMS\app\Models\Relative;
use Modules\HRMS\app\Models\RelativeType;
use Modules\HRMS\app\Models\Resume;
use Modules\HRMS\app\Models\Skill;
use Modules\PersonMS\app\Http\Repositories\PersonRepository;
use Modules\PersonMS\app\Http\Services\PersonService;
use function PHPUnit\Framework\isEmpty;

class EmployeeController extends Controller
{
//    public array $data = [];
    protected EmployeeRepository $employeeService;
    protected PersonRepository $personService;
    protected AddressRepository $addressService;
    protected RelativeRepository $relativeService;
    protected ResumeRepository $resumeService;
    protected EducationalRecordRepository $educationalRecordService;


    public function __construct(EmployeeRepository $employeeService, PersonRepository $personService, AddressRepository $addressService, RelativeRepository $relativeService, ResumeRepository $resumeService, EducationalRecordRepository $educationalRecordService)
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
    public function index(Request $request): JsonResponse
    {
        $data = $request->all();
        $perPage = $data['perPage'] ?? 10;
        $pageNum = $data['pageNum'] ?? 1;

        $result = $this->employeeService->index($perPage, $pageNum, $data);

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->all();


        $data['userID'] = \Auth::user()->id;

        if ($request->isNewAddress) {
            $addressService = new AddressRepository();
            $address = $addressService->store($data);

            if ($address instanceof \Exception) {
                return response()->json(['message' => 'خطا در وارد کردن آدرس'], 500);
            }

            $data['homeAddressID'] = $address->id;

        }
        $personService = new PersonRepository();
        $personResult = isset($request->personID) ?
            $personService->naturalUpdate($data, $data['personID']) :
            $personService->naturalStore($data);

        if ($personResult instanceof \Exception) {
            return response()->json(['message' => $personResult->getMessage()], 500);
//            return response()->json(['message' => 'خطا در افزودن شخص'], 500);
        }
        /**
         * @var Employee $employee
         */
        $employeeService = new EmployeeRepository();
        $employee = $employeeService->store($data);

        if ($employee instanceof \Exception) {
            return response()->json(['message' => 'خطا در افزودن کارمند'], 500);
        }

        //additional info insertion
        $educationalRecordService = new EducationalRecordRepository();

        $educations = $educationalRecordService->store($data['educations'],$employee->workForce->id);

//        $educationalData = json_decode($data['educations'], true);
//
//        foreach ($educationalData as $datum) {
//            $datum['workForceID'] = $employee->workForce->id;
//            $education = $educationalRecordService->store($datum);

            if ($educations instanceof \Exception) {
                return response()->json(['message' => 'خطا در افزودن تحصیلات'], 500);
            }
//        }
        $relativeService = new RelativeRepository();

        $relative = $relativeService->store($data['relatives'],$employee->workForce->id);

//        $relativeData = json_decode($data['relatives'], true);
//
//        foreach ($relativeData as $datum) {
//            $datum['WorkForceID'] = $employee->workForce->id;
//
            if ($relative instanceof \Exception) {
                return response()->json(['message' => 'خطا در افزودن تحصیلات'], 500);
            }
//        }

//        $resumesData = json_decode($data['resumes'], true);
        $resumeService = new ResumeRepository();
//
//        foreach ($resumesData as $datum) {
//            $datum['WorkForceID'] = $employee->workForce->id;
            $resume = $resumeService->store($data['resumes'],$employee->workForce->id);
            if ($resume instanceof \Exception) {
                return response()->json(['message' => 'خطا در افزودن تحصیلات'], 500);
            }
//        }


        return response()->json($employee);

    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        $employee = Employee::with('positions', 'levels', 'workForce.person.avatar', 'workForce.militaryStatus', 'workForce.educationalRecords', 'workForce.resumes', 'workForce.skills')->findOrFail($id);

        if (is_null($employee)) {
            return response()->json(['message' => 'موردی یافت نشد'], 404);

        }

        return response()->json($employee);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
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

        $personResult = $this->personService->naturalUpdate($data, $data['personID']);


        if ($personResult instanceof \Exception) {
            return response()->json(['message' => 'خطا در بروزرسانی شخص'], 500);
        }

        /**
         * @var Employee $employee
         */
        $employee = $this->employeeService->update($data, $id);

        if ($employee instanceof \Exception) {
            return response()->json(['message' => 'خطا در بروزرسانی کارمند'], 500);
        }

        $educationalData = json_decode($data['educations'], true);

        foreach ($educationalData as $datum) {
            $datum['WorkForceID'] = $employee->workForce->id;
            if (isset($datum['id'])) {
                $education = $this->educationalRecordService->update($datum, $datum['id']);

            } else {
                $education = $this->educationalRecordService->store($datum);
            }

            if ($education instanceof \Exception) {
                return response()->json(['message' => 'خطا در افزودن تحصیلات'], 500);
            }
        }

        $relativeData = json_decode($data['relatives'], true);

        foreach ($relativeData as $datum) {
            $datum['WorkForceID'] = $employee->workForce->id;
            if (isset($datum['id'])) {
                $relative = $this->relativeService->update($datum, $datum['id']);

            } else {
                $relative = $this->relativeService->store($datum);

            }


            if ($relative instanceof \Exception) {
                return response()->json(['message' => 'خطا در افزودن تحصیلات'], 500);
            }
        }

        $resumesData = json_decode($data['resumes'], true);

        foreach ($resumesData as $datum) {
            $datum['WorkForceID'] = $employee->workForce->id;
            if (isset($datum['id'])) {
                $resume = $this->resumeService->update($datum, $datum['id']);

            } else {
                $resume = $this->resumeService->store($datum);

            }


            if ($resume instanceof \Exception) {
                return response()->json(['message' => 'خطا در افزودن تحصیلات'], 500);
            }
        }

        //delete deleted workforce detail
        $deletedEducations = json_decode($data['deletedEducations'], true);
        if (!isEmpty($deletedEducations) || !is_null($deletedEducations)) {
            $deleteResult = EducationalRecord::destroy($deletedEducations);
        }
        $deletedResumes = json_decode($data['deletedResumes'], true);
        if (!isEmpty($deletedResumes) || !is_null($deletedResumes)) {
            $deleteResult = Resume::destroy($deletedResumes);
        }
        $deletedRelatives = json_decode($data['deletedRelatives'], true);
        if (!isEmpty($deletedRelatives) || !is_null($deletedRelatives)) {
            $deleteResult = Relative::destroy($deletedEducations);
        }

        return response()->json(['message' => 'با موفقیت بروزرسانی شد']);

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

    public function addEmployeeBaseInfo()
    {
        $user = \Auth::user();
        $statusID = Address::GetAllStatuses()->where('name', '=', 'فعال')->first()->id;
        $response = $user->addresses()->where('status_id', '=', $statusID)->orderBy('create_date', 'desc')->select(['id', 'title'])->get();

        $data['addressList'] = $response;
        $data['msStatusList']=MilitaryServiceStatus::all();
        $data['positionList']=Position::all();
        $data['levelList'] = Level::all();
        $data['skillList'] = Skill::all();
        $data['educationGradeList'] = LevelOfEducation::all();
        $data['relativeList'] = RelativeType::all();

        return response()->json($data);

    }
}
