<?php

namespace Modules\EMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery\Exception;
use Modules\AAA\app\Http\Traits\UserTrait;
use Modules\AAA\app\Models\Role;
use Modules\AAA\app\Models\User;
use Modules\AddressMS\app\Traits\AddressTrait;
use Modules\EMS\app\Http\Enums\RolesEnum;
use Modules\EMS\app\Http\Traits\MeetingMemberTrait;
use Modules\EMS\app\Http\Traits\MeetingTrait;
use Modules\EMS\app\Models\EnactmentTitle;
use Modules\EMS\app\Models\Meeting;
use Modules\EMS\app\Models\MeetingType;
use Modules\EMS\app\Models\MR;
use Modules\FileMS\app\Models\File;
use Modules\HRMS\app\Http\Traits\ApprovingListTrait;
use Modules\HRMS\app\Http\Traits\EducationRecordTrait;
use Modules\HRMS\app\Http\Traits\EmployeeTrait;
use Modules\HRMS\app\Http\Traits\HireTypeTrait;
use Modules\HRMS\app\Http\Traits\JobTrait;
use Modules\HRMS\app\Http\Traits\PositionTrait;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;
use Modules\HRMS\app\Http\Traits\RelativeTrait;
use Modules\HRMS\app\Http\Traits\ResumeTrait;
use Modules\HRMS\app\Http\Traits\ScriptTypeTrait;
use Modules\HRMS\app\Http\Traits\SkillTrait;
use Modules\HRMS\app\Models\HireType;
use Modules\HRMS\app\Models\Job;
use Modules\HRMS\app\Models\Position;
use Modules\HRMS\app\Models\ScriptType;
use Modules\OUnitMS\app\Models\VillageOfc;
use Modules\PersonMS\app\Http\Traits\PersonTrait;
use Modules\PersonMS\app\Models\Person;

class EMSController extends Controller
{
    use EmployeeTrait, PersonTrait, AddressTrait, RelativeTrait, ResumeTrait, EducationRecordTrait, RecruitmentScriptTrait, SkillTrait, PositionTrait, HireTypeTrait, JobTrait, ApprovingListTrait, UserTrait, ScriptTypeTrait;

    use MeetingTrait, MeetingMemberTrait;

    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    public function registerHetaatMember(Request $request): JsonResponse
    {
        $mrs = [

            'نماینده قوه قضائیه (عضو هیات تطبیق)' => [
                [
                    'scriptType' => 'انتصاب پیشفرض',
                    'hireType' => 9,
                    'job' => 'عضو هیئت',
                    'position' => RolesEnum::OZV_HEYAAT->value,
                    'levelID' => 1,

                ]
            ],
            'بخشدار (عضو هیات تطبیق)' => [
                [
                    'scriptType' => 'انتصاب پیشفرض',
                    'hireType' => 9,
                    'job' => 'عضو هیئت',
                    'position' => RolesEnum::OZV_HEYAAT->value,
                    'levelID' => 1,

                ]
            ],
            'عضو شورای شهرستان (عضو هیات تطبیق)' => [
                [
                    'scriptType' => 'انتصاب پیشفرض',
                    'hireType' => 9,
                    'job' => 'عضو هیئت',
                    'position' => RolesEnum::OZV_HEYAAT->value,
                    'levelID' => 1,

                ]
            ],

            'کارشناس مشورتی (کارشناس هیات تطبیق)' => [
                [
                    'job' => 'کارشناس مشورتی',
                    'position' => RolesEnum::KARSHENAS_MASHVARATI->value,
                    'levelID' => 1,
                    'hireType' => 9,
                    'scriptType' => 'انتصاب پیشفرض',


                ]
            ],
            'نماینده استانداری (کارشناس هیات تطبیق)' => [
                [
                    'job' => 'کارشناس مشورتی',
                    'position' => RolesEnum::KARSHENAS_MASHVARATI->value,
                    'levelID' => 1,
                    'hireType' => 9,
                    'scriptType' => 'انتصاب پیشفرض',
                ]
            ],
            'مسئول دبیرخانه و دبیر تطبیق (کارشناس هیات تطبیق)' => [
                [
                    'job' => 'کارشناس مشورتی',
                    'position' => RolesEnum::KARSHENAS_MASHVARATI->value,
                    'levelID' => 1,
                    'hireType' => 9,
                    'scriptType' => 'انتصاب پیشفرض',
                ],
                [
                    'job' => 'مسئول دبیرخانه',
                    'position' => RolesEnum::DABIR_HEYAAT->value,
                    'levelID' => 1,
                    'hireType' => 9,
                    'scriptType' => 'انتصاب دبیر',
                ]
            ],


        ];


        $data = $request->all();
        $user = User::with('person')->where('mobile', $data['mobile'])->first();
        if ($user) {
            return response()->json(['message' => 'mobile'], 422);

        }

        $person = Person::where('national_code', $data['nationalCode'])->first();

        if ($person) {
            return response()->json(['message' => 'nationalCode', 'data' => $person], 422);
        }


        try {
            DB::beginTransaction();
            $p = $user?->person;
            $personResult = !is_null($p) ?
                $this->naturalUpdate($data, $p?->personable) :
                $this->naturalStore($data);

            $data['personID'] = $personResult->person->id;
            $data['password'] = $data['nationalCode'];

            $user = $this->isPersonUserCheck($personResult->person);
            $user = $user ? $this->updateUser($data, $user) : $this->storeUser($data);

//            $meetingMember = MeetingMember::where('employee_id', $user->id)->first();
//            if ($meetingMember) {
//                return response()->json(['message' => 'این کاربر قبلا ثبت شده است'], 500);
//            }
            $mr = MR::find($data['mrID']);

            $mrInfo = $mrs[$mr->title];
            $roleNames = array_column($mrInfo, 'position');
            $roles = Role::whereIn('name', $roleNames)->get();

            $user->roles()->sync($roles->pluck('id')->toArray());

            $disabledStatusForUser = User::GetAllStatuses()->firstWhere('name', '=', 'غیرفعال');
            $user->statuses()->attach($disabledStatusForUser->id);

            if (isset($data['avatar'])) {
                $file = File::find($data['avatar']);
                $file->creator_id = $user->id;
                $file->save();
            }

            $personAsEmployee = $this->isEmployee($data['personID']);
            $employee = !is_null($personAsEmployee) ? $this->employeeUpdate($data, $personAsEmployee) : $this->employeeStore($data);

            $workForce = $employee->workForce;
            //additional info insertion


            $rs = collect($mrInfo)->map(function ($item) use ($employee, $workForce, $user, &$combo, $data) {
                $hireType = HireType::find($item['hireType']);
                $scriptType = ScriptType::where('title', $item['scriptType'])->first();
                $job = Job::where('title', $item['job'])->first();
                $position = Position::where('name', $item['position'])->first();
                $result = $this->getScriptAgentCombos($hireType, $scriptType);
                $sas = $result->map(function ($item) {
                    return [
                        'scriptAgentID' => $item->id,
                        'defaultValue' => $item->pivot->default_value ?? 0,
                    ];
                });
                $encodedSas = json_encode($sas->toArray());

                return [
                    'employeeID' => $employee->id,
                    'ounitID' => $data['ounitID'],
                    'levelID' => $item['levelID'],
                    'positionID' => $position->id,
                    'hireTypeID' => $hireType->id,
                    'scriptTypeID' => $scriptType->id,
                    'jobID' => $job->id,
                    'operatorID' => $user->id,
                    'startDate' => now(),
                    'expireDate' => now()->addYear(),
                    'scriptAgents' => $encodedSas,
                    'files' => $data['files']

                ];

            })->toArray();


            $pendingRsStatus = $this->pendingRsStatus();

            $rsRes = $this->rsStore($rs, $employee->id, $pendingRsStatus);

            if ($pendingRsStatus) {
                collect($rsRes)->each(fn($rs) => $this->approvingStore($rs));
            }


            DB::commit();
            return response()->json(['message' => 'با موفقیت ثبت شد', 'data' => $employee]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ثبت دهیار',], 500);
        }
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

    public function addBaseInfo()
    {
        $user = Auth::user();
        $titles = EnactmentTitle::all();
        $ounits = $user->activeRecruitmentScripts()
            ->whereHas('ounit', function ($query) {
                $query->where('unitable_type', VillageOfc::class)->with('ancestors');
            })
            ->whereHas('issueTime', function ($query) {
                $query->where('issue_times.title', 'شروع به همکاری');
            })
            ->with('ounit')
            ->get();


        $result = [
            'enactmentTitles' => $titles,
            'ounits' => $ounits->pluck('ounit'),
        ];

        return response()->json($result);


    }

    public function getHeyaatMembers()
    {
        $user = Auth::user();
        $user->load(['activeRecruitmentScript' => function ($q) {
            $q->orderByDesc('recruitment_scripts.create_date')
                ->limit(1)
                ->with('organizationUnit');
        }]);
        $ounit = $user?->activeRecruitmentScript[0]->organizationUnit;

        $users = User::whereHas('recruitmentScripts', function ($q) use ($ounit) {
            $q->where('organization_unit_id', $ounit->id);
        })->with('person.avatar')
            ->get(['id', 'person_id']);

        $consultingMembers = Meeting::where('isTemplate', true)->where('ounit_id', $ounit->id)
            ->with(['meetingMembers' => function ($q) use ($ounit) {
                $q->whereHas('roles', function ($query) {
                    $query->where('name', RolesEnum::KARSHENAS_MASHVARATI->value);
                })->with(['person.avatar', 'mr', 'user' => function ($query) {
                    $query->select('id');
                }]);

            }])
            ->first();

        $boardMembers = Meeting::where('isTemplate', true)->where('ounit_id', $ounit->id)
            ->with(['meetingMembers' => function ($q) use ($ounit) {
                $q->whereHas('roles', function ($query) {
                    $query->where('name', RolesEnum::OZV_HEYAAT->value);
                })->with(['person.avatar', 'mr', 'user' => function ($query) {
                    $query->select('id');
                }]);

            }])
            ->first();

        return response()->json([
            'userList' => $users,
            'consultingMembers' => $consultingMembers?->meetingMembers,
            'boardMembers' => $boardMembers?->meetingMembers
        ]);
    }

    public function updateHeyaatMembers(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $data = $request->all();
            $user->load(['activeRecruitmentScript' => function ($q) {
                $q->orderByDesc('recruitment_scripts.create_date')
                    ->limit(1)
                    ->with('organizationUnit');
            }]);
            $ounit = $user?->activeRecruitmentScript[0]->organizationUnit;

            $meeting = Meeting::where('isTemplate', true)
                ->where('ounit_id', $ounit->id)->first();

            if (is_null($meeting)) {
                $data['creatorID'] = $user->id;
                $data['meetingTypeID'] = MeetingType::where('title', 'الگو')->first()->id;
                $data['isTemplate'] = true;
                $data['ounitID'] = $ounit->id;
                $meeting = $this->storeMeeting($data);
            }

            $decode1 = json_decode($data['boardMembers'], true);
            $decode2 = json_decode($data['consultingMembers'], true);

            $ozvHeyaatRole = Role::where('name', RolesEnum::OZV_HEYAAT->value)->first();
            $karshenasMashvaratiRole = Role::where('name', RolesEnum::KARSHENAS_MASHVARATI->value)->first();


            $users1 = User::find(array_column($decode1, 'userID'));

            $users1->each(function (User $user) use ($ozvHeyaatRole) {
                $hasRole = $user->roles()->where('role_id', $ozvHeyaatRole->id)->exists();

                // Attach the role to the user if they do not have it
                if (!$hasRole) {
                    $user->roles()->attach($ozvHeyaatRole->id);
                }
            });

            $users2 = User::find(array_column($decode2, 'userID'));
            $users2->each(function (User $user) use ($karshenasMashvaratiRole) {
                $hasRole = $user->roles()->where('role_id', $karshenasMashvaratiRole->id)->exists();

                // Attach the role to the user if they do not have it
                if (!$hasRole) {
                    $user->roles()->attach($karshenasMashvaratiRole->id);
                }
            });

            $mergedData = array_merge($decode1, $decode2);

            $result = $this->bulkUpdateMeetingMembers($mergedData, $meeting);
            DB::commit();
            return response()->json(['message' => 'باموفقیت بروزرسانی شد']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در بروزرسانی', 'error' => $e->getMessage(),
                'file' => $e->getFile(),     // Get the file where the error occurred
                'line' => $e->getLine(),
                'trace' => $e->getTrace()   // Get the line number where the error occurred
            ], 500);

        }


    }

    public function getMrList()
    {
        return response()->json(MR::all());
    }
}
