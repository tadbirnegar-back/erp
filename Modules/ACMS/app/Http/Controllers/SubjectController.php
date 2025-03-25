<?php

namespace Modules\ACMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\ACC\app\Http\Traits\AccountTrait;
use Modules\ACC\app\Models\Account;
use Modules\ACMS\app\Http\Enums\SubjectTypeEnum;
use Modules\ACMS\app\Http\Trait\CircularSubjectsTrait;
use Modules\ACMS\app\Models\Circular;
use Modules\ACMS\app\Models\CircularItem;
use Modules\ACMS\app\Models\CircularSubject;
use Validator;

class SubjectController extends Controller
{
    use CircularSubjectsTrait, AccountTrait;

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
            if (isset($data['parentID'])) {
                $parent = CircularSubject::find($data['parentID']);
                $data['subjectTypeID'] = $parent->subject_type_id;
                $parentAcc = Account::where('entity_type', get_class($parent))->where('entity_id', $parent->id)->first();
            } else {
                $parentAcc = null;
            }

            $subject = $this->storeSubject($data);
            $circular = Circular::find($data['circularID']);
            $circular->circularSubjects()->attach($subject->id, [
                'percentage' => $data['percentage'] ?? 0
            ]);

            $accData = [
                'name' => $subject->name,
                'ounitID' => null,
                'segmentCode' => $subject->code,
                'entityType' => get_class($subject),
                'entityID' => $subject->id,
                'subjectID' => $subject->id,
                'categoryID' => SubjectTypeEnum::from($subject->subject_type_id)->getCategoryEnum()->value,
            ];
            //data to check subject exists
//            $rawAccData = [
//                'name' => $subject->name,
//                'ounitID' => null,
//                'segmentCode' => $subject->code,
//                'chainCode' => $subject->code,
//                'categoryID' => SubjectTypeEnum::from($subject->subject_type_id)->getCategoryEnum()->value,
//            ];

//            $existSubjectAcc = Account::where('name', $rawAccData['name'])
//                ->where('chain_code', $rawAccData['chainCode'])
//                ->where('category_id', $rawAccData['categoryID'])
//                ->where('ounit_id', $rawAccData['ounitID'])
//                ->where('segment_code', $rawAccData['segmentCode'])
//                ->first();
//
//            if ($existSubjectAcc) {
//                $existSubjectAcc->entity_type = $accData['entityType'];
//                $existSubjectAcc->entity_id = $accData['entityID'];
//                $existSubjectAcc->subject_id = $subject->id;
//                $existSubjectAcc->status_id = 150;
//                $existSubjectAcc->save();
//            } else {
            $accSubjectAccount = $this->storeAccount($accData, $parentAcc);
//            }


            DB::commit();
            return response()->json(['data' => $subject], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'error'], 500);
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
            $circularSubject = CircularSubject::with('descendantsAndSelf')->find($data['subjectID']);
            $descendants = $circularSubject->descendantsAndSelf;
            $descendants->each(function ($item) {
                $item->update(['isActive' => false]);
            });
            $subjectIDs = $descendants->pluck('id');
            $circular = Circular::find($data['circularID']);

            $circular->circularSubjects()->detach($subjectIDs->toArray());

            $subjectAccs = Account::where('entity_type', CircularSubject::class)
                ->whereIntegerInRaw('entity_id', $subjectIDs->toArray())
                ->get();
            $deleteStatus = $this->bankAccountDeactivateStatus();
            $subjectAccs->each(function ($bankAccount) use ($deleteStatus) {
                $bankAccount->statuses()->attach($deleteStatus->id);
            });


            DB::commit();
            return response()->json(['data' => $circular->circularSubjects->toHierarchy(), 'message' => 'با موفقیت حذف شد'], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'error'], 500);
        }


    }

    public function updateCircularSubject(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'itemID' => ['required', 'exists:bgt_circular_items,id'],
            'percentage' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()], 422);
        }

        try {
            $item = CircularItem::find($request->itemID);
            $item->update(['percentage' => $request->percentage]);

            return response()->json(['message' => 'باموفقیت بروزرسانی شد'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'error'], 500);
        }


    }
}
