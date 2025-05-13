<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HRMS\app\Http\Traits\EducationRecordTrait;
use Modules\HRMS\app\Models\EducationalRecord;
use Validator;

class EducationalRecordController extends Controller
{
    use EducationRecordTrait;

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
        $data = $request->all();

        $validator = Validator::make($data, [
            'fieldOfStudy' => 'required',
            'levelOfEducationalID' => 'required',
            'universityName' => 'sometimes',
            'startDate' => 'sometimes',
            'endDate' => 'sometimes',
            'average' => 'sometimes',
            'personID' => 'sometimes',
            'files' => ['sometimes', 'json'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();
            $data['personID'] = $data['personID'] ?? Auth::user()->id;
            $data['statusID'] = $this->pendingApproveEducationalRecordStatus();

            $result = $this->EducationalRecordSingleStore($data, $data['personID']);

            $this->attachEducationalRecordFiles($result, $data['files']);

            DB::commit();
            return response()->json($result);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'error'], 500);
        }

    }

    public function approveEducationalRecord(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'educationalRecordID' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();
            $status = $this->approvedEducationalRecordStatus();
            $result = EducationalRecord::find($data['educationalRecordID']);
            if (is_null($result) || $result->status_id == $status->id) {
                return response()->json(['message' => 'موردی یافت نشد'], 404);
            }
            $result->status_id = $status->id;
            $result->approver_id = Auth::user()->id;
            $result->approve_date = now();
            $result->save();

            DB::commit();
            return response()->json($result);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'error'], 500);
        }
    }

}
