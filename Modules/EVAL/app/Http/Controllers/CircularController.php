<?php

namespace Modules\EVAL\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\AAA\app\Models\User;
use Modules\EVAL\app\Http\Traits\CircularTrait;
use Modules\EVAL\app\Models\EvalCircular;
use Modules\EVAL\app\Resources\CircularFirstListResource;
use Modules\EVAL\app\Resources\DropDownResource;
use Modules\EVAL\app\Resources\ItemsListResource;
use Modules\EVAL\app\Resources\LastDataResource;
use Modules\EVAL\app\Resources\PropertiesAndvaluesResource;

class CircularController extends Controller
{
    use CircularTrait;

    public function create(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'message' => 'کاربر مورد نظر یافت نشد'
                ], 404);
            }

            DB::beginTransaction();
            $validated=$request->all();



            $circular = $this->AddCircular($validated, $user);

            if ($circular) {
                DB::commit();
                return response()->json([
                    'message' => 'بخشنامه با موفقیت ثبت شد',
                ], 201);
            } else {
                DB::rollBack();
                return response()->json([
                    'message' => 'خطا در ایجاد بخشنامه'
                ], 500);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'خطای غیرمنتظره رخ داد',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function single($circularID)
    {
        return $this->singleCircularSidebar($circularID);
    }


    public function circularSearch(Request $request)
    {
        $data = $request->all();
        $list = $this->CircularsList($data);

        return response()->json(new CircularFirstListResource($list));

    }

    public function showLastCircularData($circularID)
    {
      $list=  $this->lastDataForEditCircular($circularID);
      return response()->json(new LastDataResource($list));
    }

    public function editCircular(Request $request, $circularID)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'message' => 'کاربر مورد نظر یافت نشد'
                ], 404);
            }

            DB::beginTransaction();
            $data = $request->all();
            return response()->json($request);

            $editCircular = $this->circularEdit($circularID, $data,$user);

            if ($editCircular) {
                DB::commit();
                return response()->json([
                    'message' => 'بخشنامه با موفقیت ویرایش شد',
                    'id' => $circularID
                ], 201);
            } else {
                DB::rollBack();
                return response()->json([
                    'message' => 'خطا در ویرایش بخشنامه'
                ], 500);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'خطای غیرمنتظره رخ داد',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    public function circularDelete($circularID)
    {

        try {

            $delete = $this->deleteCircular($circularID);

            if ($delete) {
                DB::commit();
                return response()->json([
                    'message' => 'بخشنامه با موفقیت حذف گردید',
                ], 201);
            } else {
                DB::rollBack();
                return response()->json([
                    'message' => 'خطا در حذف بخشنامه'
                ], 500);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'خطای غیرمنتظره رخ داد',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    public function evaluationList()
    {
        return response()->json($this->EvaluationCompletedList());
    }

    public function itemList($circularID)
    {
        $list = $this->completingItems($circularID);

        return ItemsListResource::make($list);
    }

    public function dropDownsToAddVariable($circularID)
    {
        $dropDown = $this->requirementOfAddVariable($circularID);
        return response()->json($dropDown);
    }

    public function test(Request $request)
    {
        $data = $request->all();
        $list=$this->listing($data);
        return response()->json($list);
        return PropertiesAndvaluesResource::collection($list);

    }





}
