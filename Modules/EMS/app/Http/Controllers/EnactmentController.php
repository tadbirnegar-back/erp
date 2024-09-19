<?php

namespace Modules\EMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;
use Modules\EMS\app\Http\Enums\EnactmentStatusEnum;
use Modules\EMS\app\Http\Traits\EnactmentReviewTrait;
use Modules\EMS\app\Http\Traits\EnactmentTrait;
use Modules\EMS\app\Http\Traits\MeetingTrait;
use Modules\EMS\app\Models\Enactment;
use Modules\EMS\app\Models\EnactmentReview;
use Modules\EMS\app\Models\EnactmentStatus;
use Modules\EMS\app\Models\MeetingType;
use Modules\OUnitMS\app\Models\VillageOfc;

class EnactmentController extends Controller
{
    use EnactmentTrait, MeetingTrait, EnactmentReviewTrait;

    /**
     * Display a listing of the resource.
     */
    public function indexSecretary(Request $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->all();
        $enactments = $this->indexPendingForSecretaryStatusEnactment($data);
        $statuses = Enactment::GetAllStatuses();
        return response()->json(['data' => $enactments, 'statusList' => $statuses]);
    }

    public function indexHeyaat(Request $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->all();
        $enactments = $this->indexPendingForHeyaatStatusEnactment($data);
        $statuses = Enactment::GetAllStatuses();
        return response()->json(['data' => $enactments, 'statusList' => $statuses]);
    }

    public function indexArchive(Request $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->all();
        $enactments = $this->indexPendingForArchiveStatusEnactment($data);
        $statuses = Enactment::GetAllStatuses();
        return response()->json(['data' => $enactments, 'statusList' => $statuses]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $user = Auth::user();
            $data['creatorID'] = $user->id;
            $data['operatorID'] = $user->id;
            $data['meetingTypeID'] = MeetingType::where('title', '=', 'جلسه شورا روستا')->first()->id;

            $meeting = $this->storeMeeting($data);
            $enactment = $this->storeEnactment($data, $meeting);
            $files = json_decode($data['attachments'], true);
            $this->attachFiles($enactment, $files);
            DB::commit();
            return response()->json(['message' => 'مصوبه جدید با موفقیت ثبت شد', 'data' => $enactment], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ثبت مصوبه جدید', 'error' => $exception->getMessage()], 500);
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

        $rc = $user->activeRecruitmentScripts()->whereHas('organizationUnit')->with(['organizationUnit.descendants'])
            ->first();

        $villages = $rc?->organizationUnit->descendants()->where('unitable_type', VillageOfc::class)
            ->where(
                function ($query) use ($searchTerm) {
                    $query->whereRaw("MATCH (name) AGAINST (? IN BOOLEAN MODE)", [$searchTerm])
                        ->orWhere('name', 'like', '%' . $searchTerm . '%');
                }
            )->with('ancestors')->get();

        return response()->json($villages);
    }

    public function enactmentApproval(Request $request, $id): JsonResponse
    {

        try {
            DB::beginTransaction();
            $data = $request->all();
            $user = Auth::user();
            $enactment = Enactment::with('status')->find($id);
            if (is_null($enactment)) {
                return response()->json(['message' => 'مصوبه مورد نظر یافت نشد'], 404);
            }

            if (!$enactment->status->name == EnactmentStatusEnum::PENDING_SECRETARY_REVIEW->value) {
                return response()->json(['message' => 'امکان تغییر وضعیت مصوبه وجود ندارد'], 400);
            }

            if (isset($data['meetingID'])) {
                $enactment->meeting_id = $data['meetingID'];
                $enactment->save();
            } elseif (isset($data['meetingDate'])) {
                $data['creatorID'] = $user->id;
                $meeting = $this->storeMeeting($data);
                $enactment->meeting_id = $meeting->id;
                $enactment->save();
            }

            $heyaatStatus = $this->enactmentHeyaatStatus();

            $enactmentStatus = new EnactmentStatus();
            $enactmentStatus->enactment_id = $enactment->id;
            $enactmentStatus->status_id = $heyaatStatus->id;
            $enactmentStatus->operator_id = $user->id;
            $enactmentStatus->description = $data['description'] ?? null;
            $enactmentStatus->attachment_id = $data['attachmentID'] ?? null;
            $enactmentStatus->save();

            DB::commit();

            return response()->json(['message' => 'عملیات با موفقیت انجام شد']);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'خطا در انجام عملیات', 'error' => $e->getMessage()], 500);
        }

    }

    public function enactmentDenial(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $user = Auth::user();
            $enactment = Enactment::with('status')->find($id);
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
            DB::commit();

            return response()->json(['message' => 'عملیات با موفقیت انجام شد']);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در انجام عملیات', 'error' => $e->getMessage()], 500);
        }
    }

    public function enactmentInconsistency(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $data = $request->all();
            $reviewResult = EnactmentReview::where('enactment_id', $id)->where('user_id', $user->id)->exists();
            if ($reviewResult) {
                return response()->json(['message' => 'شما قبلا نظر خود را ثبت کرده اید'], 400);
            }
            $status = $this->reviewInconsistencyStatus();
            $enactmentReview = new EnactmentReview();
            $enactmentReview->enactment_id = $id;
            $enactmentReview->user_id = $user->id;
            $enactmentReview->status_id = $status->id;
            $enactmentReview->description = $data['description'] ?? null;
            $enactmentReview->attachment_id = $data['attachmentID'] ?? null;
            $enactmentReview->save();
            DB::commit();
            return response()->json(['message' => 'نظر شما با موفقیت ثبت شد']);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در انجام عملیات', 'error' => $e->getMessage()], 500);
        }

    }

    public function enactmentNoInconsistency($id)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $reviewResult = EnactmentReview::where('enactment_id', $id)->where('user_id', $user->id)->exists();
            if ($reviewResult) {
                return response()->json(['message' => 'شما قبلا نظر خود را ثبت کرده اید'], 400);
            }
            $status = $this->reviewNoInconsistencyStatus();
            $enactmentReview = new EnactmentReview();
            $enactmentReview->enactment_id = $id;
            $enactmentReview->user_id = $user->id;
            $enactmentReview->status_id = $status->id;
            $enactmentReview->description = $data['description'] ?? null;
            $enactmentReview->attachment_id = $data['attachmentID'] ?? null;
            $enactmentReview->save();
            DB::commit();
            return response()->json(['message' => 'نظر شما با موفقیت ثبت شد']);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در انجام عملیات', 'error' => $e->getMessage()], 500);
        }

    }


}
