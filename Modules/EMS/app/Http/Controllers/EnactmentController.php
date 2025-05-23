<?php

namespace Modules\EMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;
use Modules\AAA\app\Models\User;
use Modules\EMS\app\Http\Enums\EnactmentStatusEnum;
use Modules\EMS\app\Http\Enums\MeetingTypeEnum;
use Modules\EMS\app\Http\Enums\RolesEnum;
use Modules\EMS\app\Http\Enums\SettingsEnum;
use Modules\EMS\app\Http\Traits\EMSSettingTrait;
use Modules\EMS\app\Http\Traits\EnactmentReviewTrait;
use Modules\EMS\app\Http\Traits\EnactmentTrait;
use Modules\EMS\app\Http\Traits\MeetingTrait;
use Modules\EMS\app\Models\Enactment;
use Modules\EMS\app\Models\EnactmentReview;
use Modules\EMS\app\Models\EnactmentStatus;
use Modules\EMS\app\Models\Meeting;
use Modules\EMS\app\Models\MeetingStatus;
use Modules\EMS\app\Models\MeetingType;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\FreeZone;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\VillageOfc;

class EnactmentController extends Controller
{
    use EnactmentTrait, MeetingTrait, EnactmentReviewTrait, EMSSettingTrait;

    /**
     * Display a listing of the resource.
     */
    public function indexSecretary(Request $request): JsonResponse
    {
        $user = Auth::user();
//        $user = User::find(2174);


        $user->load(['activeDistrictRecruitmentScript.organizationUnit.ancestors']);

        $ounits = $user->load(['activeRecruitmentScript' => function ($q) {
            $q
                ->with('organizationUnit.descendantsAndSelf');
        }])?->activeRecruitmentScript?->pluck('organizationUnit.descendantsAndSelf')
            ->flatten()
            ->pluck('id')
            ->toArray();
        $data = $request->all();
        $enactments = $this->indexPendingForSecretaryStatusEnactment($data, $ounits);

//        return response()->json($enactments);
        $statuses = Enactment::GetAllStatuses();


        return response()->json(['data' => $enactments, 'statusList' => $statuses, 'Districts' => $user->activeDistrictRecruitmentScript]);
    }

    public function indexHeyaat(Request $request): JsonResponse
    {
        $user = Auth::user();
        $user->load(['activeDistrictRecruitmentScript.organizationUnit.ancestors']);

        $ounits = $user->load(['activeRecruitmentScript' => function ($q) {
            $q
                ->with('organizationUnit.descendantsAndSelf');
        }])?->activeRecruitmentScript?->pluck('organizationUnit.descendantsAndSelf')
            ->flatten()
            ->pluck('id')
            ->toArray();
        $data = $request->all();
        $enactments = $this->indexPendingForHeyaatStatusEnactment($data, $ounits);
        $statuses = Enactment::GetAllStatuses();
        return response()->json(['data' => $enactments, 'statusList' => $statuses, 'ounits' => $user->activeDistrictRecruitmentScript]);
    }

    public function indexArchive(Request $request): JsonResponse
    {
        $user = Auth::user();
        $user->load(['activeDistrictRecruitmentScript.organizationUnit.ancestors']);

        try {
            $ounits = $user->load(['activeRecruitmentScript' => function ($q) {
                $q->with('organizationUnit.descendantsAndSelf');
            }])?->activeRecruitmentScript?->pluck('organizationUnit.descendantsAndSelf')
                ->flatten()
                ->pluck('id')
                ->toArray();

            $data = $request->all();
            $enactments = $this->indexPendingForArchiveStatusEnactment($data, $ounits, $user->id);

            $statuses = Enactment::GetAllStatuses();
            $enactmentReviews = EnactmentReview::GetAllStatuses();
            return response()->json(['data' => $enactments, 'statusList' => $statuses, 'enactmentReviews' => $enactmentReviews, 'ounits' => $user->activeDistrictRecruitmentScript->pluck('organizationUnit'),
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'خطا در دریافت اطلاعات'], 500);
        }
    }

    public function indexArchiveForFreeZone(Request $request): JsonResponse
    {
        $user = Auth::user();
        $user->load(['activeFreeZoneRecruitmentScript.organizationUnit']);
        $user->load('employee');
        $freezoneIds = $user->activeFreeZoneRecruitmentScript->pluck('organizationUnit.unitable_id');

        if (!empty($freezoneIds[0])) {

            $ounitsAzad = VillageOfc::whereIntegerInRaw('free_zone_id', $freezoneIds)
                ->with('organizationUnit')
                ->get()
                ->pluck('organizationUnit');
            try {
                $data = $request->all();
                $enactments = $this->indexPendingForFreeZoneEnactment($data, $ounitsAzad->pluck('id')->toArray(), $user->id);

                $statuses = Enactment::GetAllStatuses();
                $enactmentReviews = EnactmentReview::GetAllStatuses();
                return response()->json(['data' => $enactments, 'statusList' => $statuses, 'enactmentReviews' => $enactmentReviews, 'ounits' => $ounitsAzad->pluck('id')->toArray(),
                ]);
            } catch (Exception $e) {
                return response()->json(['message' => 'خطا در دریافت اطلاعات'], 500);
            }
        } else {
            $user->load(['activeDistrictRecruitmentScript.organizationUnit.ancestors']);

            try {
                $ounits = $user->load(['activeRecruitmentScript' => function ($q) {
                    $q->with('organizationUnit.descendantsAndSelf');
                }])?->activeRecruitmentScript?->pluck('organizationUnit.descendantsAndSelf')
                    ->flatten()
                    ->pluck('id')
                    ->toArray();

                $data = $request->all();
                $enactments = $this->indexPendingForFreeZoneByDistricStatusEnactment($data, $ounits, $user->id);

                $statuses = Enactment::GetAllStatuses();
                $enactmentReviews = EnactmentReview::GetAllStatuses();
                return response()->json(['data' => $enactments, 'statusList' => $statuses, 'enactmentReviews' => $enactmentReviews, 'ounits' => $user->activeDistrictRecruitmentScript->pluck('organizationUnit'),
                ]);
            } catch (Exception $e) {
                return response()->json(['message' => 'خطا در دریافت اطلاعات'], 500);
            }
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {


            DB::beginTransaction();
            $data = $request->all();
            $validate = Validator::make($data, ['ounitID' => [
                'required',
                'exists:organization_units,id'
            ],
            ]);

            if ($validate->fails()) {
                return response()->json($validate->errors(), 422);
            }

            $user = Auth::user();
            $data['creatorID'] = $user->id;
            $data['operatorID'] = $user->id;
            //Validations

            $heyatOunit = OrganizationUnit::with([
                'ancestorsAndSelf' => function ($query) {
                    $query->where('unitable_type', DistrictOfc::class)
                        ->with(['meetingMembersHeyat' => function ($query) {
                            $query->whereHas('roles', function ($query) {
                                $query->where('name', RolesEnum::OZV_HEYAAT->value);
                            });
                        }]);
                },
            ])->find($data['ounitID']);

            $heyaatTemplateMembers = $heyatOunit->ancestorsAndSelf[0]?->meetingMembersHeyat;


            if ($heyaatTemplateMembers->isEmpty() || $heyaatTemplateMembers->count() < 2) {
                return response()->json(['message' => 'اعضا هیئت جلسه برای این بخش تعریف نشده است'], 400);
            }

            $heyaatTemplateMembers = $heyatOunit->ancestorsAndSelf[0]?->load('meetingMembersHeyat');

            $heyaatTemplateMembers = $heyatOunit->ancestorsAndSelf[0]?->meetingMembersHeyat;


            if (isset($data['meetingID'])) {
                $enactmentLimitPerMeeting = $this->getEnactmentLimitPerMeeting();

                $EncInMeetingcount = Meeting::withCount(['enactments' => function ($query) {
                    $query->whereDoesntHave('status', function ($query) {
                        $query->where('statuses.name', EnactmentStatusEnum::CANCELED->value);
                    });
                }])
                    ->first()
                    ->enactments_count;

                if ($enactmentLimitPerMeeting->value <= $EncInMeetingcount) {
                    return response()->json([
                        "message" => "جلسه انتخاب شده تکمیل ظرفیت شده است."
                    ], 422);
                }

                if ($data['meetingType'] == 2) {
                    $meetingTypeEnum = MeetingTypeEnum::SHURA_DISTRICT_MEETING;
                } else {
                    $meetingTypeEnum = MeetingTypeEnum::SHURA_MEETING;
                }
                //Shura Meeting
                $data['meetingTypeID'] = MeetingType::where('title', '=', $meetingTypeEnum)->first()->id;


                $data['meetingDate'] = $data['shuraDate'] . ' ۰۰:۰۰:۰۰';
                $meetingShura = $this->storeMeeting($data);

                $enactment = $this->storeEnactment($data, $meetingShura);

                $enactment->meetings()->attach($meetingShura->id);

                $files = json_decode($data['attachments'], true);

                $this->attachFiles($enactment, $files);

                $meeting = Meeting::find($data['meetingID']);


                foreach ($meeting->meetingMembers as $mm) {
                    $newMember = $mm->replicate(['laravel_through_key']);
                    $newMember->meeting_id = $meetingShura->id; // Set the new meeting_id
                    $newMember->save();
                }

                $meeting->enactments()->attach($enactment->id);


                //Add statuses To Enactment

                $statuses = [
                    $this->enactmentPendingSecretaryStatus()->id,
                    $this->enactmentPendingForHeyaatDateStatus()->id,
                ];

                $commonData = [
                    'enactment_id' => $enactment->id,
                    'operator_id' => $data['creatorID'],
                    'description' => $data['description'] ?? null,
                    'attachment_id' => $data['attachmentID'] ?? null,
                ];

                // Build and save each status individually to trigger the observer
                foreach ($statuses as $statusId) {
                    $statusData = array_merge($commonData, ['status_id' => $statusId]);
                    EnactmentStatus::create($statusData); // This triggers the `created` observer
                }


            } else if (isset($data['meetingDate'])) {

                //Validations

                $ancestor = "";
// Ensure ancestors are loaded and not null before attempting to access the first ancestor
                if ($heyatOunit && $heyatOunit->ancestorsAndSelf->isNotEmpty()) {
                    $ancestor = $heyatOunit->ancestorsAndSelf->first();

                    $ancestor->load('firstFreeMeetingByNow');

                }

                $firstFreeMeeting = $ancestor->firstFreeMeetingByNow;

                if (!empty($firstFreeMeeting)) {
                    return response()->json(['message' => "شما نمیتوانید با داشتن جلسه خالی جلسه دیگری ایجاد نمایید"], 404);
                }


                $currentDate = Carbon::now();
                $newMeetingDate = convertDateTimeHaveDashJalaliPersianCharactersToGregorian($data['meetingDate']);

                // Make sure $newMeetingDate is a Carbon instance
                $newMeetingDate = Carbon::parse($newMeetingDate);

                $maxDays = \DB::table('settings')
                    ->where('key', SettingsEnum::MAX_DAY_FOR_RECEPTION->value)
                    ->value('value');

                if ($newMeetingDate->lt($currentDate) || $newMeetingDate->gt($currentDate->addDays($maxDays))) {
                    return response()->json(["message" => "تاریخ انتخاب شده درست نیست"], 404);
                }

                //End Of Validations


                $meetingDate = $data['meetingDate'];
                $data['meetingDate'] = $data['shuraDate'] . ' ۰۰:۰۰:۰۰';
                if ($data['meetingType'] == 2) {
                    $meetingTypeEnum = MeetingTypeEnum::SHURA_DISTRICT_MEETING;
                } else {
                    $meetingTypeEnum = MeetingTypeEnum::SHURA_MEETING;
                }
                //Shura Meeting
                $data['meetingTypeID'] = MeetingType::where('title', '=', $meetingTypeEnum)->first()->id;
                $meetingShura = $this->storeMeeting($data);

                $data['meetingDate'] = $meetingDate;

                $data['meetingTypeID'] = MeetingType::where('title', '=', MeetingTypeEnum::HEYAAT_MEETING->value)->first()->id;


                $data['ounitID'] = $ancestor->id;
                $meetingHeyaat = $this->storeMeeting($data);

                //Make Enactments

                $enactment = $this->storeEnactment($data, $meetingShura);
                $enactment->meetings()->attach($meetingShura->id);
                $enactment->meetings()->attach($meetingHeyaat->id);

                //Add statuses to enactment
                $statuses = [
                    $this->enactmentPendingSecretaryStatus()->id,
                    $this->enactmentPendingForHeyaatDateStatus()->id,
                ];

                $commonData = [
                    'enactment_id' => $enactment->id,
                    'operator_id' => $data['creatorID'],
                    'description' => $data['description'] ?? null,
                    'attachment_id' => $data['attachmentID'] ?? null,
                ];

                // Build and save each status individually to trigger the observer
                foreach ($statuses as $statusId) {
                    $statusData = array_merge($commonData, ['status_id' => $statusId]);
                    EnactmentStatus::create($statusData); // This triggers the `created` observer
                }

                $files = json_decode($data['attachments'], true);

                $this->attachFiles($enactment, $files);


                foreach ($heyaatTemplateMembers as $mm) {
                    $newMember = $mm->replicate(['laravel_through_key']);
                    $newMember->meeting_id = $meetingHeyaat->id; // Set the new meeting_id
                    $newMember->save();


                    $newMember = $mm->replicate(['laravel_through_key']);
                    $newMember->meeting_id = $meetingShura->id; // Set the new meeting_id
                    $newMember->save();
                }

            }

            DB::commit();
            return response()->json(['message' => 'مصوبه جدید با موفقیت ثبت شد', 'data' => $enactment], 200);

        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['message' => 'مصوبه جدید ثبت نشد', $exception->getMessage(), $exception->getTrace()], 500);

        }
    }

    public function addEnactmentFreeZone(Request $request)
    {
        try {


            DB::beginTransaction();
            $data = $request->all();

            $validate = Validator::make($data, ['ounitID' => [
                'required',
                'exists:organization_units,id'
            ],
            ]);

            if ($validate->fails()) {
                return response()->json($validate->errors(), 422);
            }

            $user = Auth::user();
            $data['creatorID'] = $user->id;
            $data['operatorID'] = $user->id;
            //Validations
            $village_id = OrganizationUnit::find($data['ounitID'])->unitable_id;
            $free_zone_id = VillageOfc::find($village_id)->free_zone_id;


            $heyatOunit = OrganizationUnit::with([
                'ancestorsAndSelf' => function ($query) {
                    $query->where('unitable_type', DistrictOfc::class)
                        ->with(['meetingMembers' => function ($query) {
                            $query->whereHas('roles', function ($query) {
                                $query->where('name', RolesEnum::OZV_HEYAAT->value);
                            });
                        }]);
                },
            ])->find($data['ounitID']);

            $heyatOunitForMember = OrganizationUnit::with([
                'ancestorsAndSelf' => function ($query) {
                    $query->where('unitable_type', DistrictOfc::class)
                        ->with(['meetingMembersAzad' => function ($query) {
                            $query->whereHas('roles', function ($query) {
                                $query->where('name', RolesEnum::OZV_HEYAT_FREEZONE->value);
                            });
                        }]);
                },
            ])->find($data['ounitID']);

            $heyaatTemplateMembers = $heyatOunitForMember->ancestorsAndSelf[0]?->meetingMembersAzad;


            if ($heyaatTemplateMembers->isEmpty() || $heyaatTemplateMembers->count() < 2) {
                return response()->json(['message' => 'اعضا هیئت جلسه برای این بخش تعریف نشده است'], 400);
            }

            $heyaatTemplateMembers = $heyatOunitForMember->ancestorsAndSelf[0]?->load('meetingMembersAzad');

            $heyaatTemplateMembers = $heyatOunitForMember->ancestorsAndSelf[0]?->meetingMembersAzad;


            if (isset($data['meetingID'])) {
                $enactmentLimitPerMeeting = $this->getEnactmentLimitPerMeeting();

                $EncInMeetingcount = Meeting::withCount(['enactments' => function ($query) {
                    $query->whereDoesntHave('status', function ($query) {
                        $query->where('statuses.name', EnactmentStatusEnum::CANCELED->value);
                    });
                }])
                    ->first()
                    ->enactments_count;

                if ($enactmentLimitPerMeeting->value <= $EncInMeetingcount) {
                    return response()->json([
                        "message" => "جلسه انتخاب شده تکمیل ظرفیت شده است."
                    ], 422);
                }


                $meetingTypeEnum = MeetingTypeEnum::SHURA_MEETING;

                //Shura Meeting
                $data['meetingTypeID'] = MeetingType::where('title', '=', $meetingTypeEnum)->first()->id;


                $data['meetingDate'] = $data['shuraDate'] . ' ۰۰:۰۰:۰۰';
                $meetingShura = $this->storeMeeting($data);

                $enactment = $this->storeEnactment($data, $meetingShura);

                $enactment->meetings()->attach($meetingShura->id);

                $files = json_decode($data['attachments'], true);

                $this->attachFiles($enactment, $files);

                $meeting = Meeting::find($data['meetingID']);


                foreach ($meeting->meetingMembers as $mm) {
                    $newMember = $mm->replicate(['laravel_through_key']);
                    $newMember->meeting_id = $meetingShura->id; // Set the new meeting_id
                    $newMember->save();
                }

                $meeting->enactments()->attach($enactment->id);


                //Add statuses To Enactment

                $statuses = [
                    $this->enactmentPendingSecretaryStatus()->id,
                    $this->enactmentPendingForHeyaatDateStatus()->id,
                ];

                $commonData = [
                    'enactment_id' => $enactment->id,
                    'operator_id' => $data['creatorID'],
                    'description' => $data['description'] ?? null,
                    'attachment_id' => $data['attachmentID'] ?? null,
                ];

                // Build and save each status individually to trigger the observer
                foreach ($statuses as $statusId) {
                    $statusData = array_merge($commonData, ['status_id' => $statusId]);
                    EnactmentStatus::create($statusData); // This triggers the `created` observer
                }


            } else if (isset($data['meetingDate'])) {

                //Validations
                $ancestor = "";
// Ensure ancestors are loaded and not null before attempting to access the first ancestor
                if ($heyatOunit && $heyatOunit->ancestorsAndSelf->isNotEmpty()) {
                    $ancestor = $heyatOunit->ancestorsAndSelf->first();

                    $ancestor->load('firstFreeMeetingByNowForFreeZone');

                }


                $firstFreeMeeting = $ancestor->firstFreeMeetingByNowForFreeZone;

                if (!empty($firstFreeMeeting)) {
                    return response()->json(['message' => "شما نمیتوانید با داشتن جلسه خالی جلسه دیگری ایجاد نمایید"], 404);
                }


                $currentDate = Carbon::now();
                $newMeetingDate = convertDateTimeHaveDashJalaliPersianCharactersToGregorian($data['meetingDate']);

                // Make sure $newMeetingDate is a Carbon instance
                $newMeetingDate = Carbon::parse($newMeetingDate);

                $maxDays = \DB::table('settings')
                    ->where('key', SettingsEnum::MAX_DAY_FOR_RECEPTION->value)
                    ->value('value');

                if ($newMeetingDate->lt($currentDate) || $newMeetingDate->gt($currentDate->addDays($maxDays))) {
                    return response()->json(["message" => "تاریخ انتخاب شده درست نیست"], 404);
                }

                //End Of Validations


                $meetingDate = $data['meetingDate'];
                $data['meetingDate'] = $data['shuraDate'] . ' ۰۰:۰۰:۰۰';


                $meetingTypeEnum = MeetingTypeEnum::SHURA_MEETING;

                //Shura Meeting
                $data['meetingTypeID'] = MeetingType::where('title', '=', $meetingTypeEnum)->first()->id;
                $meetingShura = $this->storeMeeting($data);

                $data['meetingDate'] = $meetingDate;

                $data['meetingTypeID'] = MeetingType::where('title', '=', MeetingTypeEnum::FREE_ZONE->value)->first()->id;


                $data['ounitID'] = $ancestor->id;
                $meetingHeyaat = $this->storeMeeting($data);

                //Make Enactments

                $enactment = $this->storeEnactment($data, $meetingShura);
                $enactment->meetings()->attach($meetingShura->id);
                $enactment->meetings()->attach($meetingHeyaat->id);

                //Add statuses to enactment
                $statuses = [
                    $this->enactmentPendingSecretaryStatus()->id,
                    $this->enactmentPendingForHeyaatDateStatus()->id,
                ];

                $commonData = [
                    'enactment_id' => $enactment->id,
                    'operator_id' => $data['creatorID'],
                    'description' => $data['description'] ?? null,
                    'attachment_id' => $data['attachmentID'] ?? null,
                ];

                // Build and save each status individually to trigger the observer
                foreach ($statuses as $statusId) {
                    $statusData = array_merge($commonData, ['status_id' => $statusId]);
                    EnactmentStatus::create($statusData); // This triggers the `created` observer
                }

                $files = json_decode($data['attachments'], true);

                $this->attachFiles($enactment, $files);


                foreach ($heyaatTemplateMembers as $mm) {
                    $newMember = $mm->replicate(['laravel_through_key']);
                    $newMember->meeting_id = $meetingHeyaat->id; // Set the new meeting_id
                    $newMember->save();


                    $newMember = $mm->replicate(['laravel_through_key']);
                    $newMember->meeting_id = $meetingShura->id; // Set the new meeting_id
                    $newMember->save();
                }

            }

            DB::commit();
            return response()->json(['message' => 'مصوبه جدید با موفقیت ثبت شد', 'data' => $enactment], 200);

        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['message' => 'مصوبه جدید ثبت نشد', $exception->getMessage(), $exception->getTrace()], 500);

        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        $enactment = Enactment::with('status')->find($id);
        $user = Auth::user();
        if (is_null($enactment)) {
            return response()->json(['message' => 'مصوبه مورد نظر یافت نشد'], 404);
        }

        $componentsToRenderWithData = $this->enactmentShow($enactment, $user);
        $enactment->setAttribute('componentsToRender', $componentsToRenderWithData);
        $enactment->setAttribute('enactmentLimitPerMeeting', $this->getEnactmentLimitPerMeeting());
        $enactment->setAttribute('shouraMaxMeetingDateDaysAgo', $this->getShouraMaxMeetingDateDaysAgo());
        return response()->json($enactment);
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

    public function getMyVillagesToAddEnactment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $searchTerm = $request->name;

        $rc = $user->load('activeRecruitmentScripts');

        $villages = $rc?->organizationUnit->descendants()->where('unitable_type', VillageOfc::class)
            ->where(
                function ($query) use ($searchTerm) {
                    $query->whereRaw("MATCH (name) AGAINST (? IN BOOLEAN MODE)", [$searchTerm])
                        ->orWhere('name', 'like', '%' . $searchTerm . '%');
                }
            )->with('ancestors', 'unitable')->get();

        return response()->json($villages);
    }

    public function enactmentApproval(Request $request, $id): JsonResponse
    {

        try {
            DB::beginTransaction();
            $data = $request->all();
            $user = Auth::user();
            $enactment = Enactment::with('status', 'meeting')->find($id);
            if (is_null($enactment)) {
                return response()->json(['message' => 'مصوبه مورد نظر یافت نشد'], 404);
            }

            if ($enactment->status->name != EnactmentStatusEnum::PENDING_SECRETARY_REVIEW->value) {
                return response()->json(['message' => 'امکان تغییر وضعیت مصوبه وجود ندارد'], 400);
            }

            if (isset($data['meetingID'])) {
                $meeting = Meeting::find($data['meetingID']);
                $enactment->meetings()->attach($meeting->id);
//                $enactment->meeting_id = $meeting->id;
//                $enactment->save();
            } elseif (isset($data['meetingDate'])) {
                $villageID = $enactment->meeting->ounit_id;
                $data['creatorID'] = $user->id;
                $data['meetingTypeID'] = MeetingType::where('title', '=', 'جلسه هیئت تطبیق')->first()->id;

                $villageWithDistrict = OrganizationUnit::with(['ancestors' => function ($q) {
                    $q->where('unitable_type', DistrictOfc::class);

                }])->find($villageID);

                $data['ounitID'] = $villageWithDistrict->ancestors[0]->id;
                $meeting = $this->storeMeeting($data);
                $enactment->meetings()->attach($meeting->id);

//                $enactment->meeting_id = $meeting->id;
//                $enactment->save();

                $meetingTemplate = Meeting::where('isTemplate', true)
                    ->where('ounit_id', $data['ounitID'])
                    ->with('meetingMembers')->first();


                if (!is_null($meetingTemplate)) {
                    foreach ($meetingTemplate->meetingMembers as $mm) {
                        $newMM = $mm->replicate(['laravel_through_key']);
                        $newMM->meeting_id = $meeting->id;
                        $newMM->save();
                    }
                }

            }
            $pendingForHeyaatDateStatus = $this->enactmentPendingForHeyaatDateStatus();

            $enactmentStatus = new EnactmentStatus();
            $enactmentStatus->enactment_id = $enactment->id;
            $enactmentStatus->status_id = $pendingForHeyaatDateStatus->id;
            $enactmentStatus->operator_id = $user->id;
            $enactmentStatus->description = $data['description'] ?? null;
            $enactmentStatus->attachment_id = $data['attachmentID'] ?? null;
            $enactmentStatus->save();

            DB::commit();

            return response()->json(['message' => 'عملیات با موفقیت انجام شد']);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'خطا در انجام عملیات', 'error' => 'error'], 500);
        }

    }

    public function enactmentDenial(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $user = Auth::user();
            $enactment = Enactment::with(['status', 'latestMeeting'])->find($id);

            if (is_null($enactment)) {
                return response()->json(['message' => 'مصوبه مورد نظر یافت نشد'], 404);
            }

            if (!$enactment->status->name == EnactmentStatusEnum::CANCELED->value) {
                return response()->json(['message' => 'امکان تغییر وضعیت مصوبه وجود ندارد'], 400);
            }
            $cancelStatus = $this->enactmentCancelStatus();

            $enactmentStatus = new EnactmentStatus();
            $enactmentStatus->enactment_id = $enactment->id;
            $enactmentStatus->status_id = $cancelStatus->id;
            $enactmentStatus->operator_id = $user->id;
            $enactmentStatus->description = $data['description'] ?? null;
            $enactmentStatus->attachment_id = $data['attachmentID'] ?? null;
            $enactmentStatus->save();


            //latest meeting check for emptiness
            $latestMeeting = $enactment->latestMeeting;
            $latestMeeting->loadCount(['enactments' => function ($query) {
                $query->whereDoesntHave('status', function ($query) {
                    $query->where('statuses.id', $this->enactmentCancelStatus()->id);
                });
            }]);

            if ($latestMeeting->enactments_count == 0) {
                $status = $this->meetingCancelStatus();
                $meetingStatus = new MeetingStatus();
                $meetingStatus->meeting_id = $latestMeeting->id;
                $meetingStatus->status_id = $status->id;
                $meetingStatus->operator_id = $user->id;
                $meetingStatus->save();

            }


            DB::commit();

            return response()->json(['message' => 'عملیات با موفقیت انجام شد']);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در انجام عملیات', 'error' => 'error',], 500);
        }
    }

    public function enactmentInconsistency(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $data = $request->all();
            $enactment = Enactment::find($id);
            $status = $this->reviewInconsistencyStatus();

            EnactmentReview::updateOrCreate(
                ['enactment_id' => $id, 'user_id' => $user->id],
                [
                    'status_id' => $status->id,
                    'description' => $data['description'] ?? null,
                    'attachment_id' => $data['attachmentID'] ?? null
                ]
            );

            $AllMainPersons = $enactment->load( ['members' => function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->whereIn('name', [RolesEnum::OZV_HEYAAT->value , RolesEnum::OZV_HEYAT_FREEZONE]);
                });
            }]);

            $AllMainCount = $AllMainPersons->members->count();

            $reviewStatuses = $enactment->enactmentReviews()
                ->whereHas('user.roles', function ($query) {
                    $query->whereIn('name', [RolesEnum::OZV_HEYAAT->value, RolesEnum::OZV_HEYAT_FREEZONE->value]);
                })->with('status')->get();

            if ($reviewStatuses->count() > 1) {
                $result = $reviewStatuses->groupBy('status.id')
                    ->map(fn($statusGroup) => [
                        'status' => $statusGroup->first(),
                        'count' => $statusGroup->count()
                    ])
                    ->sortByDesc('count')
                    ->values();

                if ($result->count() == 2 && isset($result[0]) && isset($result[1]) && $result[0]['count'] == $result[1]['count']) {
                    $finalStatus = null;
                } else {
                    $finalStatus = $result[0]['status']->status;
                }

                if (!is_null($finalStatus)) {
                    $enactment->final_status_id = $finalStatus->id;
                    $enactment->save();
                }

            }

            if ($reviewStatuses->count() == $AllMainCount) {
                $heyaatStatus = $this->enactmentCompleteStatus();

                $enactmentStatus = new EnactmentStatus();
                $enactmentStatus->enactment_id = $id;
                $enactmentStatus->status_id = $heyaatStatus->id;
                $enactmentStatus->operator_id = $user->id;
                $enactmentStatus->description = $data['description'] ?? null;
                $enactmentStatus->attachment_id = $data['attachmentID'] ?? null;
                $enactmentStatus->save();
            }

            DB::commit();
            return response()->json(['message' => 'نظر شما با موفقیت ثبت شد']);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در انجام عملیات', 'error' => 'error'], 500);
        }

    }

    public function enactmentNoInconsistency(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $enactment = Enactment::find($id);
            $status = $this->reviewNoInconsistencyStatus();
            $data = $request->all();
            EnactmentReview::updateOrCreate(
                ['enactment_id' => $id, 'user_id' => $user->id],
                [
                    'status_id' => $status->id,
                    'description' => $data['description'] ?? null,
                    'attachment_id' => $data['attachmentID'] ?? null
                ]
            );

            $reviewStatuses = $enactment->enactmentReviews()
                ->whereHas('user.roles', function ($query) {
                    $query->whereIn('name', [RolesEnum::OZV_HEYAAT->value, RolesEnum::OZV_HEYAT_FREEZONE->value]);
                })->with('status')->get();

            $AllMainPersons = $enactment->load( ['members' => function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->whereIn('name', [RolesEnum::OZV_HEYAAT->value , RolesEnum::OZV_HEYAT_FREEZONE]);
                });
            }]);

            $AllMainCount = $AllMainPersons->members->count();

            if ($reviewStatuses->count() > 1) {
                $result = $reviewStatuses->groupBy('status.id')
                    ->map(fn($statusGroup) => [
                        'status' => $statusGroup->first(),
                        'count' => $statusGroup->count()
                    ])
                    ->sortByDesc('count')
                    ->values();

                if ($result->count() == 2 && isset($result[0]) && isset($result[1]) && $result[0]['count'] == $result[1]['count']) {
                    $finalStatus = null;
                } else {
                    $finalStatus = $result[0]['status']->status;
                }

                if (!is_null($finalStatus)) {
                    $enactment->final_status_id = $finalStatus->id;
                    $enactment->save();
                }

            }

            if ($reviewStatuses->count() == $AllMainCount) {
                $heyaatStatus = $this->enactmentCompleteStatus();

                $enactmentStatus = new EnactmentStatus();
                $enactmentStatus->enactment_id = $id;
                $enactmentStatus->status_id = $heyaatStatus->id;
                $enactmentStatus->operator_id = $user->id;
                $enactmentStatus->description = $data['description'] ?? null;
                $enactmentStatus->attachment_id = $data['attachmentID'] ?? null;
                $enactmentStatus->save();
            }
            DB::commit();
            return response()->json(['message' => 'نظر شما با موفقیت ثبت شد']);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در انجام عملیات', 'error' => 'error'], 500);
        }

    }


}
