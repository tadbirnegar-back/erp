<?php

namespace Modules\EMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\EMS\app\Http\Traits\EnactmentTrait;
use Modules\EMS\app\Http\Traits\MeetingTrait;
use Modules\EMS\app\Models\Enactment;
use Modules\EMS\app\Models\MeetingType;
use Modules\OUnitMS\app\Models\VillageOfc;

class EnactmentController extends Controller
{
    use EnactmentTrait, MeetingTrait;

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
}
