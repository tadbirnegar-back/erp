<?php

namespace App\Http\Controllers;


use Modules\AAA\app\Models\User;
use Modules\EMS\app\Http\Enums\RolesEnum;
use Modules\EMS\app\Http\Traits\EnactmentTrait;
use Modules\EMS\app\Models\Meeting;

class testController extends Controller
{
    use EnactmentTrait;


    public function run()
    {
        $json1 = '[{"userID":2001,"mrName":"بخشدار","meetingMemberID":"4"},{"userID":2002,"mrName":"قاضی","meetingMemberID":"5"},{"userID":2003,"mrName":"عضو شورای شهرستان","meetingMemberID":"6"}]';
        $json2 = '[{"userID":1905,"mrName":"نماینده استانداری","meetingMemberID":"1"},{"userID":2000,"mrName":"مسئول دبیرخانه","meetingMemberID":"2"},{"userID":2004,"mrName":"کارشناس مشورتی","meetingMemberID":"7"}]';
        $decode1 = json_decode($json1, true);
        $decode2 = json_decode($json2, true);
        dd(array_merge($decode1, $decode2));
        $ounit = User::with(['activeRecruitmentScript' => function ($q) {
            $q->orderByDesc('recruitment_scripts.create_date')
                ->limit(1)
                ->with('organizationUnit.descendantsAndSelf');
        }])->find(1905)->activeRecruitmentScript[0]->organizationUnit;
        dd($ounit);
        $members = Meeting::where('ounit_id', $ounit->id)
            ->with(['meetingMembers' => function ($q) use ($ounit) {
                $q->whereHas('roles', function ($query) {
                    $query->where('name', RolesEnum::KARSHENAS_MASHVARATI->value);
                })->with('person.avatar');

            }])
            ->first();

        $members = Meeting::where('ounit_id', $ounit->id)
            ->with(['meetingMembers' => function ($q) use ($ounit) {
                $q->whereHas('roles', function ($query) {
                    $query->where('name', RolesEnum::KARSHENAS_MASHVARATI->value);
                })->with('person.avatar');

            }])
            ->first();
//
//        $users = User::whereHas('recruitmentScripts', function ($q) use ($ounit) {
//            $q->whereIntegerInRaw('organization_unit_id', $ounit);
//        })->with('person.avatar')->get();
//        dd($users);

//        $enactment = Enactment::with('consultingMembers')->find(3);
//        dd($enactment);
//        $user = User::find(1905);
//        $componentsToRenderWithData = $this->enactmentShow($enactment, $user);
//        dd($componentsToRenderWithData);

//        $startDate = '2023-01-01';
//        $endDate = '2024-12-31';
//        $enactments = Enactment::whereHas('enactmentReviews.user.roles', function ($query) {
//            $query->where('name', 'مدیرکل');
//        }, '>', 0)
//            ->with(['enactmentReviews' => function ($query) {
//                $query->select('enactment_id', 'status_id', DB::raw('count(*) as review_count'))
//                    ->groupBy('enactment_id', 'status_id');
//            }])
//            ->get();

//        $enactments = Enactment::whe

//            Enactment::whereHas('members', function ($q) {
//            $q->where('employee_id', 1905);
//        })

//            ->select('meeting_id', DB::raw('count(*) as enactment_count'))
//            ->whereBetween('create_date', [$startDate, $endDate])
//            ->groupBy('meeting_id')
//            ->get();
//        dd($enactments);

//        $m = Meeting::with('meetingMembers.person', 'meetingMembers.mr')->find(13);
//        dd($m);
//
//        $userRoles = ['admin', 'بخشدار'];
//        $enactmentStatus = 'در انتظار وصول';
//
//        $componentsToRender = $this->getComponentsToRender($userRoles, $enactmentStatus);
//        dd($componentsToRender->all());
//        $a = Enactment::with([
//            'enactmentReviews' => function ($query) {
//                $query
//                    ->where('user_id', 1905);
//            }
//
//
//        ])->find(4);
//
//        dd(
//
//            $a
////                ->load(['enactmentReviews.meetingMembers' => function ($query) use ($a) {
////                $query->where('meeting_id', $a->meeting_id);
////            }])
//
//        );
//
//        $componentsToRender = collect([
//            'MainEnactment' => ['reviewStatuses', 'meeting', 'attachments', 'creator'],
//            'MembersBeforeReview' => ['meetingMembers.person', 'meetingMembers.mr'],
//            'AcceptDenyBtns' => ['relatedDates' => function ($query) {
//                $query->where('meetings.meeting_date', '>', now());
//            }],
//            'ReviewCards' => ['meetingMembers.enactmentReviews' => function ($query) {
//                $query->where('enactment_id', 4);
//            },],
//
//            'DenyCard' => ['canceledStatus.meetingMember'],
//            'ReviewBtn' => ['enactmentReviews' => function ($query) {
//                $query->where('user_id', 1905);
//            }]
//        ]);
//
//        $myPermissions = collect(['myProfile', 'MainEnactment', 'DenyCard', 'xyz']);
//
//        $uniqueValues = $componentsToRender->only($myPermissions->intersect($componentsToRender->keys()))
//            ->flatten()
//            ->unique()
//            ->values()
//            ->toArray();
//
//        $enactment = Enactment::with($uniqueValues)->find(2);
//
//        $componentsWithData = $componentsToRender->map(fn($relations, $component) => [
//            'name' => $component,
//            'data' => $enactment->only($relations)
//        ])->values();
//
//        $enactment->setAttribute('componentsToRender', $componentsWithData);
//
////        return response()->json($a);
//
//        dd($componentsWithData, $enactment);
    }

}

