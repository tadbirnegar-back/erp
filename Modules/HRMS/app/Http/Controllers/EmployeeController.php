<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;
use Modules\AddressMS\app\Models\Address;
use Modules\AddressMS\app\Repositories\AddressRepository;
use Modules\AddressMS\app\services\AddressService;
use Modules\AddressMS\app\Traits\AddressTrait;
use Modules\HRMS\app\Http\Repositories\EducationalRecordRepository;
use Modules\HRMS\app\Http\Repositories\EmployeeRepository;
use Modules\HRMS\app\Http\Repositories\RecruitmentScriptRepository;
use Modules\HRMS\app\Http\Repositories\RelativeRepository;
use Modules\HRMS\app\Http\Repositories\ResumeRepository;
use Modules\HRMS\app\Http\Services\EducationalRecordService;
use Modules\HRMS\app\Http\Services\EmployeeService;
use Modules\HRMS\app\Http\Services\RelativeService;
use Modules\HRMS\app\Http\Services\ResumeService;
use Modules\HRMS\app\Http\Traits\EducationRecordTrait;
use Modules\HRMS\app\Http\Traits\EmployeeTrait;
use Modules\HRMS\app\Http\Traits\HireTypeTrait;
use Modules\HRMS\app\Http\Traits\JobTrait;
use Modules\HRMS\app\Http\Traits\PositionTrait;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;
use Modules\HRMS\app\Http\Traits\RelativeTrait;
use Modules\HRMS\app\Http\Traits\ResumeTrait;
use Modules\HRMS\app\Http\Traits\SkillTrait;
use Modules\HRMS\app\Models\EducationalRecord;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\HireType;
use Modules\HRMS\app\Models\Job;
use Modules\HRMS\app\Models\Level;
use Modules\HRMS\app\Models\LevelOfEducation;
use Modules\HRMS\app\Models\MilitaryServiceStatus;
use Modules\HRMS\app\Models\Position;
use Modules\HRMS\app\Models\Relative;
use Modules\HRMS\app\Models\RelativeType;
use Modules\HRMS\app\Models\Resume;
use Modules\HRMS\app\Models\ScriptAgent;
use Modules\HRMS\app\Models\ScriptType;
use Modules\HRMS\app\Models\Skill;
use Modules\PersonMS\app\Http\Repositories\PersonRepository;
use Modules\PersonMS\app\Http\Services\PersonService;
use Modules\PersonMS\app\Http\Traits\PersonTrait;
use Modules\PersonMS\app\Models\Religion;
use Modules\PersonMS\app\Models\ReligionType;
use Modules\StatusMS\app\Models\Status;
use function PHPUnit\Framework\isEmpty;
use function Sodium\add;

class EmployeeController extends Controller
{
    use EmployeeTrait, PersonTrait, AddressTrait, RelativeTrait, ResumeTrait, EducationRecordTrait, RecruitmentScriptTrait, SkillTrait, PositionTrait, HireTypeTrait,JobTrait;

//    public array $data = [];
//    protected EmployeeRepository $employeeService;
//    protected PersonRepository $personService;
//    protected AddressRepository $addressService;
//    protected RelativeRepository $relativeService;
//    protected ResumeRepository $resumeService;
//    protected EducationalRecordRepository $educationalRecordService;
//
//
//    public function __construct(EmployeeRepository $employeeService, PersonRepository $personService, AddressRepository $addressService, RelativeRepository $relativeService, ResumeRepository $resumeService, EducationalRecordRepository $educationalRecordService)
//    {
//        $this->employeeService = $employeeService;
//        $this->personService = $personService;
//        $this->addressService = $addressService;
//        $this->relativeService = $relativeService;
//        $this->resumeService = $resumeService;
//        $this->educationalRecordService = $educationalRecordService;
//    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $data = $request->all();
        $perPage = $data['perPage'] ?? 10;
        $pageNum = $data['pageNum'] ?? 1;

        $result = $this->employeeIndex($perPage, $pageNum, $data);

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->all();

        try {
            DB::beginTransaction();

            $data['userID'] = \Auth::user()->id;

            if ($request->isNewAddress) {
                $address = $this->addressStore($data);

                $data['homeAddressID'] = $address->id;

            }

            $personResult = isset($request->personID) ?
                $this->naturalUpdate($data, $data['personID']) :
                $this->naturalStore($data);

            $data['personID'] = $personResult->person->id;
            $personAsEmployee = $this->isEmployee($data['personID']);
            $employee = !is_null($personAsEmployee) ? $this->employeeUpdate($data, $personAsEmployee) : $this->employeeStore($data);

            $workForce = $employee->workForce;
            //additional info insertion
            if (isset($data['educations'])) {
                $edus = json_decode($data['educations'], true);

                $educations = $this->EducationalRecordStore($edus, $workForce->id);

            }


            if (isset($data['relatives'])) {
                $rels = json_decode($data['relatives'], true);

                $relatives = $this->RelativeStore($rels, $workForce->id);

            }


            if (isset($data['resumes'])) {
                $resumes = json_decode($data['resumes'], true);

                $resume = $this->resumeStore($resumes, $workForce->id);

            }


            if (isset($data['recruitmentRecords'])) {
                $rs = json_decode($data['recruitmentRecords'], true);

                $rsRes = $this->rsStore($rs, $employee->id);

            }
            DB::commit();
            return response()->json($employee);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در افزودن کارمند', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        $employee = Employee::with(
            'positions',
            'levels',
            'workForce.person.avatar',
            'workForce.militaryStatus',
            'workForce.educationalRecords',
            'workForce.resumes',
            'workForce.skills')
            ->findOr($id, function () {
                return response()
                    ->json(['message' => 'موردی یافت نشد'], 404);
            });

        return response()->json($employee);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $employee = Employee::with(

            'workForce.person',
        )
            ->findOr($id, function () {
                return response()
                    ->json(['message' => 'موردی یافت نشد'], 404);
            });

        $data = $request->all();


        try {
            DB::beginTransaction();
            $data['userID'] = \Auth::user()->id;

            if ($request->isNewAddress) {
                $address = $this->addressStore($data);


                $data['homeAddressID'] = $address->id;

            }

            $personResult = $this->naturalUpdate($data, $employee->workForce->person->personable_id);


            /**
             * @var Employee $employee
             */
            $employee = $this->employeeUpdate($data, $id);


            $educationalData = json_decode($data['educations'], true);
            $educationalUpsert = $this->EducationalRecordBulkUpdate($educationalData, $employee->workForce->id);


            $relativeData = json_decode($data['relatives'], true);
            $relatives = $this->relativeBulkUpdate($relativeData, $employee->workForce->id);

            $resumesData = json_decode($data['resumes'], true);
            $resumes = $this->resumeUpdate($resumesData, $employee->workForce->id);

            //delete deleted workforce detail
            if (isset($data['deletedEducations'])) {
                $deletedEducations = json_decode($data['deletedEducations'], true);
                EducationalRecord::destroy($deletedEducations);
            }

            if (isset($data['deletedResumes'])) {
                $deletedResumes = json_decode($data['deletedResumes'], true);
                Resume::destroy($deletedResumes);
            }

            if (isset($data['deletedRelatives'])) {
                $deletedRelatives = json_decode($data['deletedRelatives'], true);
                Relative::destroy($deletedRelatives);
            }

            DB::commit();
            return response()->json(['message' => 'با موفقیت بروزرسانی شد']);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در بروزرسانی کارمند'], 500);

        }
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
        $result = $this->naturalExists($request->nationalCode);
        if ($result == null) {
            $message = 'notFound';
            $data = null;
        } elseif ($this->isEmployee($result->id)) {
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
        if (!is_null($user)) {
            $statusID = $this->activeAddressStatus()->id;

            $response = $user->addresses()->where('status_id', '=', $statusID)->orderBy('create_date', 'desc')->select(['id', 'title'])->get();
            $data['addressList'] = $response;

        }


        $data['skillList'] = $this->skillIndex();
        $data['religion'] = Religion::all();
        $data['religionType'] = ReligionType::all();
        $data['hireTypes'] = $this->getAllHireTypes();
        $data['jobs'] = $this->getListOfJobs();


        return response()->json($data);

    }

    public function findPersonToInsertAsEmployee(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'nationalCode' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

//        $person = $this->latestScriptByNationalCode($data['nationalCode']);

        $person = $this->naturalExists($request->nationalCode);
        $employee = $person ? $this->isEmployee($person->id) : null;

        $message = $person ? ($employee ? 'employee' : 'found') : 'notFound';
        $data = $person?->setAttribute('employee', $employee);

        if ($employee) {
            $employee = $this->loadLatestActiveScript($employee);
            $scriptTypes = $this->getCompatibleIssueTimesByName($employee->latestRecruitmentScript ? $employee->latestRecruitmentScript->issueTime->title : null);
        } else {
            $scriptTypes = $this->getCompatibleIssueTimesByName();
        }

        return response()->json(['data' => $data, 'scriptTypes' => $scriptTypes, 'message' => $message]);


    }

    public function agentCombos(Request $request)
    {
        $data = $request->all();

        $hireType = HireType::find($data['hireTypeID']);
        $scriptType = ScriptType::find($data['scriptTypeID']);

        $result = $this->getScriptAgentCombos($hireType, $scriptType);

        return response()->json($result);
    }

    public function employeeListFilter()
    {
        $response['statuses'] = Status::where('model', Employee::class)->get();
        $response['positions'] = Position::all();
        $response['scriptTypes'] = ScriptType::all();

        return response()->json($response);

    }
}
