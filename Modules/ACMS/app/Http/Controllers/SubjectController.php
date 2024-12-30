<?php

namespace Modules\ACMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\ACMS\app\Http\Trait\CircularSubjectsTrait;
use Modules\ACMS\app\Models\Circular;
use Modules\ACMS\app\Models\CircularSubject;
use Validator;

class SubjectController extends Controller
{
    use CircularSubjectsTrait;

    public function storeSubjectAndAttachToCircular(Request $request): JsonResponse
    {
        $data = $request->all();
        $user = Auth::user();

        $validate = Validator::make($data, [
            'subjectName' => 'required',
            'code' => 'required',
            'circularID' => ['required', 'exists:bgt_circulars,id'],
            'oldItemID' => 'sometimes',
            'parentID' => 'sometimes',
        ]);

        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()], 422);
        }

        $data['userID'] = $user->id;

        try {
            DB::beginTransaction();

            $subject = $this->storeSubject($data);
            $circular = Circular::find($data['circularID']);
            $circular->circularSubjects()->attach($subject->id);

            DB::commit();
            return response()->json(['data' => $subject], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function deActiveSubjectAndDetachToCircular(Request $request): JsonResponse
    {
        $data = $request->all();
        $user = Auth::user();

        $validate = Validator::make($data, [
            'subjectID' => ['required', 'exists:bgt_circular_subjects,id'],
            'circularID' => ['required', 'exists:bgt_circulars,id'],

        ]);

        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()], 422);
        }
        try {
            DB::beginTransaction();
            $circularSubject = CircularSubject::with('descendantsAndSelf')->find($data['circularSubjectID']);
            $descendants = $circularSubject->descendantsAndSelf;
            $descendants->update(['isActive' => false]);
            $subjectIDs = $descendants->pluck('id')->toArray();
            $circular = Circular::find($data['circularID']);

            $circular->circularSubjects()->detach($subjectIDs);
            DB::commit();
            return response()->json(['message' => 'با موفقیت حذف شد'], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }


    }
}
