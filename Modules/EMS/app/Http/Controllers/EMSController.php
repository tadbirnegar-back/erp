<?php

namespace Modules\EMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery\Exception;
use Modules\AAA\app\Http\Enums\UserRolesEnum;
use Modules\AAA\app\Http\Traits\UserTrait;
use Modules\AAA\app\Models\User;
use Modules\AddressMS\app\Traits\AddressTrait;
use Modules\EMS\app\Http\Enums\RolesEnum;
use Modules\EMS\app\Http\Traits\EMSSettingTrait;
use Modules\EMS\app\Http\Traits\EnactmentTitleTrait;
use Modules\EMS\app\Http\Traits\MeetingMemberTrait;
use Modules\EMS\app\Http\Traits\MeetingTrait;
use Modules\EMS\app\Models\EnactmentTitle;
use Modules\EMS\app\Models\Meeting;
use Modules\EMS\app\Models\MeetingType;
use Modules\EMS\app\Models\MR;
use Modules\EMS\app\Resources\FreeZoneLiveSearchResource;
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
use Modules\HRMS\app\Notifications\RegisterNotification;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\VillageOfc;
use Modules\PersonMS\app\Http\Traits\PersonTrait;
use Modules\PersonMS\app\Models\Person;
use Validator;

class EMSController extends Controller
{
    use EmployeeTrait, PersonTrait, AddressTrait, RelativeTrait, ResumeTrait, EducationRecordTrait, RecruitmentScriptTrait, SkillTrait, PositionTrait, HireTypeTrait, JobTrait, ApprovingListTrait, UserTrait, ScriptTypeTrait, EMSSettingTrait, EnactmentTitleTrait, EMSSettingTrait;

    use MeetingTrait, MeetingMemberTrait;

    public array $data = [];

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
        //

        return response()->json($this->data);
    }

    public function registerHeyaatMember(Request $request): JsonResponse
    {

        $validator = Validator::make($request->all(), [
            'mobile' => 'required|unique:users,mobile',
            'nationalCode' => 'required|unique:persons,national_code',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 422);
        }
        $mrs = [

            'نماینده قوه قضائیه (عضو هیات تطبیق)' => [
                [
                    'scriptType' => 'انتصاب هیئت تطبیق',
                    'hireType' => 9,
                    'job' => 'عضو هیئت',
                    'position' => 'نماینده قوه قضائیه',
                    'levelID' => 1,

                ]
            ],
            'بخشدار (عضو هیات تطبیق)' => [
                [
                    'scriptType' => 'انتصاب بخشدار',
                    'hireType' => 9,
                    'job' => 'عضو هیئت',
                    'position' => 'بخشدار',
                    'levelID' => 1,

                ]
            ],
            'عضو شورای شهرستان (عضو هیات تطبیق)' => [
                [
                    'scriptType' => 'انتصاب هیئت تطبیق',
                    'hireType' => 9,
                    'job' => 'عضو هیئت',
                    'position' => 'عضو شورای شهرستان',
                    'levelID' => 1,

                ]
            ],

            'کارشناس مشورتی (کارشناس هیات تطبیق)' => [
                [
                    'job' => 'کارشناس مشورتی',
                    'position' => 'کارشناس مشورتی',
                    'levelID' => 1,
                    'hireType' => 9,
                    'scriptType' => 'انتصاب هیئت تطبیق',


                ]
            ],
            'نماینده استانداری (کارشناس هیات تطبیق)' => [
                [
                    'job' => 'کارشناس مشورتی',
                    'position' => 'نماینده استانداری',
                    'levelID' => 1,
                    'hireType' => 9,
                    'scriptType' => 'انتصاب هیئت تطبیق',
                ]
            ],
            'مسئول دبیرخانه و دبیر تطبیق (کارشناس هیات تطبیق)' => [
                [
                    'job' => 'مسئول دبیرخانه',
                    'position' => 'مسئول دبیرخانه',
                    'levelID' => 1,
                    'hireType' => 9,
                    'scriptType' => 'انتصاب دبیر',
                ]
            ],


        ];


        $data = $request->all();
        $user = User::with(['person' => function ($query) use ($data) {
            $query->where('national_code', $data['nationalCode']);
        }])->where('mobile', $data['mobile'])->first();


        try {
            DB::beginTransaction();
            $person = $user?->person;
            $personResult = !is_null($person) ?
                $this->naturalUpdate($data, $person?->personable) :
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


//            $roleNames = array_column($mrInfo, 'position');
//            $roles = Role::whereIn('name', $roleNames)->get();
//
//            $user->roles()->sync($roles->pluck('id')->toArray());

//            $disabledStatusForUser = User::GetAllStatuses()->firstWhere('name', '=', 'غیرفعال');
//            $user->statuses()->attach($disabledStatusForUser->id);

            if (isset($data['avatar'])) {
                $file = File::find($data['avatar']);
                $file->creator_id = $user->id;
                $file->save();
            }


            $personAsEmployee = $this->isEmployee($data['personID']);
            $employee = !is_null($personAsEmployee) ? $this->employeeUpdate($data, $personAsEmployee) : $this->employeeStore($data);


            $workForce = $employee->workForce;
            //additional info insertion


            $scripts = json_decode($data['script'], true);


            $hireType = HireType::find($mrInfo[0]['hireType']);
            $scriptType = ScriptType::where('title', $mrInfo[0]['scriptType'])->first();
            $job = Job::where('title', $mrInfo[0]['job'])->first();
            $position = Position::where('name', $mrInfo[0]['position'])->first();

            $result = $this->getScriptAgentCombos($hireType, $scriptType);
            $sas = $result->map(function ($item) {
                return [
                    'scriptAgentID' => $item->id,
                    'defaultValue' => $item->pivot->default_value ?? 0,
                ];
            });
            $encodedSas = json_encode($sas->toArray());

            foreach ($scripts as $script) {

                $duration = (int)$scriptType->duration->value;
                if ($duration == 0) {
                    $expireDate = null;
                } else {
                    $startDateRaw = $script->startDate;
                    $expireDate = Carbon::parse($startDateRaw)->addMonths($duration);
                }

                $entry = [
                    'employeeID' => $employee->id,
                    'ounitID' => $script["ounitID"],
                    'levelID' => $mrInfo[0]['levelID'],
                    'positionID' => $position->id,
                    'hireTypeID' => $hireType->id,
                    'scriptTypeID' => $scriptType->id,
                    'jobID' => $job->id,
                    'operatorID' => $user->id,
                    'startDate' => $script["startDate"] ?? now(),
                    'expireDate' => $expireDate,
                    'scriptAgents' => $encodedSas,
                    'files' => $script["files"] ?? null, // Match file by index or set as null if not found
                ];

                $pendingRsStatus = $this->pendingRsStatus();


                // Store each entry individually
                $this->rsStore([$entry], $employee->id, $pendingRsStatus);
            }


            $username = Person::find($user->person_id)->display_name;

            $user->notify((new RegisterNotification($username))->onQueue('default'));
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
        $ounits = $user->activeRecruitmentScript()
            ->whereHas('ounit', function ($query) {
                $query->where('unitable_type', VillageOfc::class)->with('ancestors');
            })
            ->with('ounit')
            ->get();


        $districtOunits = $user->activeRecruitmentScript()
            ->whereHas('ounit', function ($query) {
                $query->where('unitable_type', DistrictOfc::class);
            })
            ->whereHas('scriptType', function ($query) {
                $query->where('title', 'انتصاب دبیر');
            })
            ->with('ounit.ancestors')
            ->get();


        $result = [
            'enactmentTitles' => $titles,
            'shouraMaxMeetingDateDaysAgo' => $this->getShouraMaxMeetingDateDaysAgo(),
            'receiptionMaxDays' => $this->getReceptionMaxDays(),
            'emsEnactmentLimitPerMeeting' => $this->getEnactmentLimitPerMeeting(),
            'ounits' => $ounits->pluck('ounit'),
            'districtOunits' => $districtOunits->pluck('ounit'),
        ];

        return response()->json($result);


    }

    public function getHeyaatMembers()
    {
        try {
            $user = Auth::user();
            $user->load(['activeRecruitmentScript.organizationUnit.meetingMembers.roles', 'activeRecruitmentScript.organizationUnit.meetingMembers.user']);


            $ounit = $user->activeRecruitmentScript[0]->organizationUnit;

            $consultingMembers = collect();
            $boardMembers = collect();

            if ($ounit->meetingMembers->isNotEmpty()) {
                $ounit->meetingMembers->each(function ($member) use (&$consultingMembers, &$boardMembers) {
                    $member->roles->each(function ($role) use ($member, &$consultingMembers, &$boardMembers) {
                        if ($role->name === RolesEnum::KARSHENAS_MASHVARATI->value) {
                            $consultingMembers->push($member);
                        } elseif ($role->name === RolesEnum::OZV_HEYAAT->value) {
                            $boardMembers->push($member);
                        }
                    });
                });

                $consultingMembers = $consultingMembers->unique('id')->values();
                $boardMembers = $boardMembers->unique('id')->values();
            }
            $usersBakhshdarOzvHeyaat = User::whereHas('recruitmentScripts', function ($q) use ($ounit) {
                $q->where('organization_unit_id', $ounit->id);
            })
                ->whereHas('roles', function ($q) {
                    $q->where('name', UserRolesEnum::BAKHSHDAR); // Replace with your actual first condition
                })
                ->whereHas('roles', function ($q) {
                    $q->where('name', UserRolesEnum::OZV_HEYAAT); // Replace with your actual second condition
                })
                ->with('person.avatar')
                ->get(['id', 'person_id']);

            $usersDabirKarshenas = User::whereHas('recruitmentScripts', function ($q) use ($ounit) {
                $q->where('organization_unit_id', $ounit->id);
            })
                ->whereHas('roles', function ($q) {
                    $q->where('name', UserRolesEnum::DABIRKHANE->value); // Replace with your actual first condition
                })
                ->whereHas('roles', function ($q) {
                    $q->where('name', UserRolesEnum::KARSHENAS->value); // Replace with your actual second condition
                })
                ->with('person.avatar')
                ->get(['id', 'person_id']);


            $usersKarshenas = User::whereHas('recruitmentScripts', function ($q) use ($ounit) {
                $q->where('organization_unit_id', $ounit->id);
            })
                ->whereHas('roles', function ($q) {
                    $q->where('name', UserRolesEnum::KARSHENAS->value); // Replace with your actual first condition
                })
                ->whereDoesntHave('roles', function ($q) {
                    $q->where('name', '=', UserRolesEnum::DABIRKHANE->value); // Replace with your actual second condition
                })
                ->with('person.avatar')
                ->get(['id', 'person_id']);

            $usersHeyaat = User::whereHas('recruitmentScripts', function ($q) use ($ounit) {
                $q->where('organization_unit_id', $ounit->id);
            })
                ->whereHas('roles', function ($q) {
                    $q->where('name', UserRolesEnum::OZV_HEYAAT->value)->where('name', '!=', UserRolesEnum::BAKHSHDAR->value); // Replace with your actual first condition
                })
                ->whereDoesntHave('roles', function ($q) {
                    $q->where('name', '=', UserRolesEnum::BAKHSHDAR->value); // Replace with your actual second condition
                })
                ->with('person.avatar')
                ->get(['id', 'person_id']);


            return response()->json([
                'candidates' => [
                    "ozv_heyaat" => $usersHeyaat,
                    "karshenas" => $usersKarshenas,
                    "dabir_karshenas" => $usersDabirKarshenas,
                    "bakhshdar_heyaat" => $usersBakhshdarOzvHeyaat,
                ],
                'members' => [
                    'consultingMembers' => $consultingMembers,
                    'boardMembers' => $boardMembers
                ],

            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'خطا در دریافت اعضای هیات', 'error' => 'error',
            ], 500);
        }

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

//            $ozvHeyaatRole = Role::where('name', RolesEnum::OZV_HEYAAT->value)->first();
//            $karshenasMashvaratiRole = Role::where('name', RolesEnum::KARSHENAS_MASHVARATI->value)->first();


//            $users1 = User::find(array_column($decode1, 'userID'));
//
//            $users1->each(function (User $user) use ($ozvHeyaatRole) {
//                $hasRole = $user->roles()->where('role_id', $ozvHeyaatRole->id)->exists();
//
//                // Attach the role to the user if they do not have it
////                if (!$hasRole) {
//                $user->roles()->sync($ozvHeyaatRole->id);
////                }
//            });


//            $userToAttachDabirRole = collect($decode2)->first(function ($item) {
//                return $item['mrName'] == 'مسئول دبیرخانه و دبیر تطبیق (کارشناس هیات تطبیق)';
//            });
//
//            $users2 = User::find(array_column($decode2, 'userID'));
//            $users2->each(function (User $user) use ($karshenasMashvaratiRole, $userToAttachDabirRole) {
//                $hasRole = $user->roles()->where('role_id', $karshenasMashvaratiRole->id)->exists();
//
//                // Attach the role to the user if they do not have it
////                if (!$hasRole) {
//                $user->roles()->sync($karshenasMashvaratiRole->id);
//                if ($user->id == $userToAttachDabirRole['userID']) {
//                    $dabirRole = Role::where('name', RolesEnum::DABIR_HEYAAT->value)->first();
//                    $user->roles()->attach($dabirRole->id);
//                }
////                }
//            });

            $mergedData = array_merge($decode1, $decode2);

            $result = $this->bulkUpdateMeetingMembers($mergedData, $meeting);
            DB::commit();
            return response()->json(['message' => 'باموفقیت بروزرسانی شد']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در بروزرسانی', 'error' => 'error',
            ], 500);

        }


    }

    public function getMrList()
    {
        return response()->json(MR::all());
    }

    public function getHeyaatMembersByOunit(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'ounitID' => 'required|exists:organization_units,id'
        ]);

        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()], 422);
        }

        try {
            $ounit = OrganizationUnit::with('meetingMembers.roles', 'meetingMembers.user')->find($request->ounitID);

            $consultingMembers = collect();
            $boardMembers = collect();

            if ($ounit->meetingMembers->isNotEmpty()) {
                $ounit->meetingMembers->each(function ($member) use (&$consultingMembers, &$boardMembers) {
                    $member->roles->each(function ($role) use ($member, &$consultingMembers, &$boardMembers) {
                        if ($role->name === RolesEnum::KARSHENAS_MASHVARATI->value) {
                            $consultingMembers->push($member);
                        } elseif ($role->name === RolesEnum::OZV_HEYAAT->value) {
                            $boardMembers->push($member);
                        }
                    });
                });

                $consultingMembers = $consultingMembers->unique('id')->values();
                $boardMembers = $boardMembers->unique('id')->values();
            }
            $usersBakhshdarOzvHeyaat = User::whereHas('recruitmentScripts', function ($q) use ($ounit) {
                $q->where('organization_unit_id', $ounit->id);
            })
                ->whereHas('roles', function ($q) {
                    $q->where('name', UserRolesEnum::BAKHSHDAR); // Replace with your actual first condition
                })
                ->whereHas('roles', function ($q) {
                    $q->where('name', UserRolesEnum::OZV_HEYAAT); // Replace with your actual second condition
                })
                ->with('person.avatar')
                ->get(['id', 'person_id']);

            $usersDabirKarshenas = User::whereHas('recruitmentScripts', function ($q) use ($ounit) {
                $q->where('organization_unit_id', $ounit->id);
            })
                ->whereHas('roles', function ($q) {
                    $q->where('name', UserRolesEnum::DABIRKHANE->value); // Replace with your actual first condition
                })
                ->whereHas('roles', function ($q) {
                    $q->where('name', UserRolesEnum::KARSHENAS->value); // Replace with your actual second condition
                })
                ->with('person.avatar')
                ->get(['id', 'person_id']);


            $usersKarshenas = User::whereHas('recruitmentScripts', function ($q) use ($ounit) {
                $q->where('organization_unit_id', $ounit->id);
            })
                ->whereHas('roles', function ($q) {
                    $q->where('name', UserRolesEnum::KARSHENAS->value); // Replace with your actual first condition
                })
                ->whereDoesntHave('roles', function ($q) {
                    $q->where('name', '=', UserRolesEnum::DABIRKHANE->value); // Replace with your actual second condition
                })
                ->with('person.avatar')
                ->get(['id', 'person_id']);

            $usersHeyaat = User::whereHas('recruitmentScripts', function ($q) use ($ounit) {
                $q->where('organization_unit_id', $ounit->id);
            })
                ->whereHas('roles', function ($q) {
                    $q->where('name', UserRolesEnum::OZV_HEYAAT->value)->where('name', '!=', UserRolesEnum::BAKHSHDAR->value); // Replace with your actual first condition
                })
                ->whereDoesntHave('roles', function ($q) {
                    $q->where('name', '=', UserRolesEnum::BAKHSHDAR->value); // Replace with your actual second condition
                })
                ->with('person.avatar')
                ->get(['id', 'person_id']);

            return response()->json([
                'candidates' => [
                    "ozv_heyaat" => $usersHeyaat,
                    "karshenas" => $usersKarshenas,
                    "dabir_karshenas" => $usersDabirKarshenas,
                    "bakhshdar_heyaat" => $usersBakhshdarOzvHeyaat,
                ],
                'members' => [
                    'consultingMembers' => $consultingMembers,
                    'boardMembers' => $boardMembers
                ],
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'خطا در دریافت اعضای هیات'], 500);
        }


    }

    public function updateHeyaatMembersByOunit(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'ounitID' => 'required|exists:organization_units,id',
            'boardMembers' => 'required',
            'consultingMembers' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()], 422);
        }
        $data = $request->all();
        $user = Auth::user();

        try {
            DB::beginTransaction();
            $meeting = Meeting::firstOrCreate(
                ['isTemplate' => true, 'ounit_id' => $request->ounitID],
                [
                    'creator_id' => $user->id,
                    'meeting_type_id' => MeetingType::where('title', 'الگو')->first()->id,
                    'isTemplate' => true,
                    'ounit_id' => $request->ounitID,
                    'create_date' => now(),
                ]
            );

            $decode1 = json_decode($data['boardMembers'], true);
            $decode2 = json_decode($data['consultingMembers'], true);

//            $ozvHeyaatRole = Role::where('name', RolesEnum::OZV_HEYAAT->value)->first();
//            $karshenasMashvaratiRole = Role::where('name', RolesEnum::KARSHENAS_MASHVARATI->value)->first();
//
//            $users1 = User::find(array_column($decode1, 'userID'));
//            $users2 = User::find(array_column($decode2, 'userID'));
//
//            $users1->each(fn(User $user) => $user->roles()->sync([$ozvHeyaatRole->id]));
//            $users2->each(fn(User $user) => $user->roles()->sync([$karshenasMashvaratiRole->id]));

            $mergedData = array_merge($decode1, $decode2);

            $result = $this->bulkUpdateMeetingMembers($mergedData, $meeting);
            DB::commit();
            return response()->json(['message' => 'باموفقیت بروزرسانی شد']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'خطا در بروزرسانی', 'error' => 'error',
            ], 500);
        }
    }

    public function getDistrictOfcsWithMembersCount()
    {
        $districtOfcs = OrganizationUnit::where('unitable_type', DistrictOfc::class)->withCount('meetingMembers')->with('ancestors')->get();

        return response()->json($districtOfcs);
    }


    public function getAutoNoMoghayeratSettings()
    {

        $enactmentLimitPerMeeting = $this->getEnactmentLimitPerMeeting();
        $shouraMaxMeetingDateDaysAgo = $this->getShouraMaxMeetingDateDaysAgo();
        $receiptionMaxDays = $this->getReceptionMaxDays();

        return response()->json([
            'enactmentLimitPerMeeting' => $enactmentLimitPerMeeting,
            'shouraMaxMeetingDateDaysAgo' => $shouraMaxMeetingDateDaysAgo,
            'receiptionMaxDays' => $receiptionMaxDays,
        ]);
    }

//    public function getAutoNoMoghayeratSettings()
//    {
//        $consultingAutoMoghayerat = $this->getConsultingAutoMoghayerat();
//        $boardAutoMoghayerat = $this->getBoardAutoMoghayerat();
//        $enactmentLimitPerMeeting = $this->getEnactmentLimitPerMeeting();
//        $shouraMaxMeetingDateDaysAgo = $this->getShouraMaxMeetingDateDaysAgo();
//
//        return response()->json([
//            'consultingAutoMoghayerat' => $consultingAutoMoghayerat,
//            'boardAutoMoghayerat' => $boardAutoMoghayerat,
//            'enactmentLimitPerMeeting' => $enactmentLimitPerMeeting,
//            'shouraMaxMeetingDateDaysAgo' => $shouraMaxMeetingDateDaysAgo,
//        ]);
//    }

    public function updateAutoMoghayeratSettings(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'emsMaxDayForReception' => 'required',
            'enactmentLimitPerMeeting' => 'required',
            'shouraMaxMeetingDateDaysAgo' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()], 422);
        }

        try {
            DB::beginTransaction();
            $emsReciept = $this->updateReceptionMaxDays($request->emsMaxDayForReception);
            $entLimit = $this->updateEnactmentLimitPerMeeting($request->enactmentLimitPerMeeting);
            $shouraMaxMeetingDateDaysAgo = $this->updateShouraMaxMeetingDateDaysAgo($request->shouraMaxMeetingDateDaysAgo ?? 0);

            DB::commit();
            return response()->json(['message' => 'با موفقیت بروزرسانی شد', 'data' => [
                'receiptionMaxDays' => $emsReciept,
                'enactmentLimitPerMeeting' => $entLimit,
                'shouraMaxMeetingDateDaysAgo' => $shouraMaxMeetingDateDaysAgo
            ]]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در بروزرسانی', 'error' => 'error',
                'file' => $e->getFile(),     // Get the file where the error occurred
                'line' => $e->getLine(),
                'trace' => 'error'   // Get the line number where the error occurred
            ], 500);
        }
    }

    public function getEnactmentTitlesIndex()
    {
        return response()->json($this->enactmentTitleIndex());
    }

    public function updateEnactmentTitle(Request $request, $id)
    {
        $enactmentTitle = EnactmentTitle::find($id);

        if (is_null($enactmentTitle)) {
            return response()->json(['message' => 'موردی یافت نشد'], 404);
        }

        try {
            DB::beginTransaction();
            $this->enactmentTitleUpdate($request->all(), $enactmentTitle);
            DB::commit();
            return response()->json(['message' => 'با موفقیت ویرایش شد',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ویرایش مصوبه',
            ], 500);
        }
    }

    public function storeEnactmentTitle(Request $request)
    {


        try {
            DB::beginTransaction();
            $enactmentTitle = $this->enactmentTitleStore($request->all());
            DB::commit();
            return response()->json(['message' => 'با موفقیت اضافه شد',
                'data' => $enactmentTitle
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ویرایش مصوبه',
            ], 500);
        }
    }

    public function destroyEnactmentTitle($id)
    {
        $enactmentTitle = EnactmentTitle::find($id);

        if (is_null($enactmentTitle)) {
            return response()->json(['message' => 'موردی یافت نشد'], 404);
        }

        try {
            DB::beginTransaction();
            $this->enactmentTitleDestroy($enactmentTitle);
            DB::commit();
            return response()->json(['message' => 'با موفقیت حذف شد',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در حذف عنوان مصوبه',
            ], 500);
        }
    }


    public function liveSearch(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $searchTerm = $request->name;

        $user->load(['activeDistrictRecruitmentScript.ounit.descendants']);


        $ounits = $user->activeDistrictRecruitmentScript->pluck('ounit.descendants');


        $filteredOunits = collect($ounits)->flatMap(function ($units) {
            return $units->filter(function ($unit) {
                return $unit->unitable_type === VillageOfc::class;
            })->pluck('unitable_id');
        });

// Ensure $ounits is a collection of Eloquent models
        $villageOfc = VillageOfc::whereIntegerInRaw('id', $filteredOunits)
            ->whereNull('free_zone_id')
            ->whereHas('organizationUnit', function ($query) use ($searchTerm) {
                $query
                    ->where(function ($query) use ($searchTerm) {
                        $query->whereRaw("MATCH (name) AGAINST (? IN BOOLEAN MODE)", [$searchTerm])
                            ->orWhere('name', 'like', '%' . $searchTerm . '%');
                    });
            })
            ->with(['organizationUnit.ancestors' => function ($query) {
                $query->orderByDesc('id');
            }])
            ->get();

        $response = $villageOfc->each(function ($item) {
            $item->organizationUnit->setAttribute('abadiCode', $item->abadi_code);
        })->pluck('organizationUnit');


        return response()->json($response);
    }


    public function registerHeyaatRequirement()
    {
        $mr = MR::all();
        $genders = DB::table('genders')->get();

        $orginizationUnit = OrganizationUnit::where('unitable_type', DistrictOfc::class)->with('ancestors')->get();

        return response()->json([
            "mr" => $mr,
            "genders" => $genders,
            "orginizationUnit" => $orginizationUnit
        ]);
    }

    public function liveSearchFreeZone(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $searchTerm = $request->name;

        $user->load(['activeRsForSearchFz.organizationUnit']);
        $freezoneIds = $user->activeRsForSearchFz->pluck('organizationUnit.unitable_id');

        $ounits = VillageOfc::whereIntegerInRaw('free_zone_id', $freezoneIds)
            ->whereHas('organizationUnit', function ($query) use ($searchTerm) {
                $query
                    ->where(function ($query) use ($searchTerm) {
                        $query->whereRaw("MATCH (name) AGAINST (? IN BOOLEAN MODE)", [$searchTerm])
                            ->orWhere('name', 'like', '%' . $searchTerm . '%');
                    });
            })
            ->with(['organizationUnit.ancestors' => function ($query) {
                $query->orderByDesc('id'); // Replace 'id' with the appropriate column for reverse ordering
            }])
            ->get();
        $response = $ounits->each(function ($item) {
            $item->organizationUnit->setAttribute('abadiCode', $item->abadi_code);
        })->pluck('organizationUnit');

        return response()->json($response);
    }
}
