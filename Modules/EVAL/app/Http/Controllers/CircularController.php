<?php

namespace Modules\EVAL\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\AAA\app\Models\User;
use Modules\EVAL\app\Http\Traits\CircularTrait;
use Modules\EVAL\app\Models\EvalCircular;
use Modules\EVAL\app\Resources\ItemsListResource;

class CircularController extends Controller
{
    use CircularTrait;

    public function create(Request $request)
    {
        try {
            $user = User::find(1889);
            if (!$user) {
                return response()->json([
                    'message' => 'کاربر مورد نظر یافت نشد'
                ], 404);
            }

            DB::beginTransaction();

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:255',
                'maximumValue' => 'required|integer|min:1',
                'fileID' => 'required|integer|exists:files,id',
                'deadline' => 'required|integer',
            ]);

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
        return response()->json($this->CircularsList($data));

    }

    public function showLastCircularData($circularID)
    {
        return response()->json($this->lastDataForEditCircular($circularID));
    }

    public function editCircular(Request $request, $circularID)
    {
        try {

            DB::beginTransaction();
            $data=$request->all();
            $editCircular = $this->circularEdit($circularID,$data);

            if ($editCircular) {
                DB::commit();
                return response()->json([
                    'message' => 'بخشنامه با موفقیت ویرایش شد',
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

            $delete=$this->deleteCircular($circularID);

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

    public function arzyabiList()
    {
        return response()->json ($this->arzyabiEnrollmentList());
    }

    public function itemList()
    {
        $list=$this->completingItems();
        return ItemsListResource::make($list);
    }


}
