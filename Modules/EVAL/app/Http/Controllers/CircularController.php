<?php

namespace Modules\EVAL\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\AAA\app\Models\User;
use Modules\AddressMS\app\Models\District;
use Modules\EVAL\app\Http\Traits\CircularTrait;
use Modules\EVAL\app\Models\EvalCircular;
use Modules\EVAL\app\Resources\CircularFirstListResource;
use Modules\EVAL\app\Resources\DropDownResource;
use Modules\EVAL\app\Resources\ItemsListResource;
use Modules\EVAL\app\Resources\LastDataResource;
use Modules\EVAL\app\Resources\PropertiesAndvaluesResource;
use Modules\EVAL\App\Resources\SingleResource;
use Modules\LMS\app\Models\OucProperty;
use Modules\LMS\app\Models\OucPropertyValue;
use Modules\LMS\app\Resources\OucPropertyListResource;
use Modules\LMS\app\Resources\OucPropertyValueListResource;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\VillageOfc;

class CircularController extends Controller
{
    use CircularTrait;

    public function create(Request $request)
    {
        try {
//            $user = Auth::user();
            $user=User::find(1889);
            if (!$user) {
                return response()->json([
                    'message' => 'کاربر مورد نظر یافت نشد'
                ], 403);
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
       $data = $this->singleCircularSidebar($circularID);
        $circular = $data['data']->first();

//       dd($data);
       return[
           'data' => response()->json(new SingleResource($circular)),
           'completedCircularCount' => $data['completedCircularCount']
       ];
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
                ], 403);
            }

            DB::beginTransaction();
            $data = $request->all();

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
        $user = Auth::user();
        return response()->json($this->EvaluationCompletedList($user));
    }
    public function listForDistrict()
    {
//        $user = Auth::user();
        $user = User::find(1955);


       $districtList= $this->listOfDistrict($user);
        if (!$user) {
         return response()->json([
             'message' => 'شما بخشدار هیج سازمانی نمی باشید'
         ], 403);
     }
        return response()->json($districtList);
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

    public function createVariable(Request $request,$circularID )
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'message' => 'کاربر مورد نظر یافت نشد'
                ], 403);
            }

            DB::beginTransaction();
            $data = $request->all();

            $editVariable = $this->addVariableSection($circularID, $data);

            if ($editVariable) {
                DB::commit();
                return response()->json([
                    'message' => 'متغیر با موفقیت ثبت شد',
                    'id' => $circularID
                ], 201);
            } else {
                DB::rollBack();
                return response()->json([
                    'message' => 'خطا در ثبت متغیر'
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

    public function updateVariable(Request $request,$circularID)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'message' => 'کاربر مورد نظر یافت نشد'
                ], 403);
            }

            DB::beginTransaction();
            $data = $request->all();

            $editVariable = $this->editVariable($circularID, $data);

            if ($editVariable) {
                DB::commit();
                return response()->json([
                    'message' => 'متغیر با موفقیت ویرایش شد',
                    'id' => $circularID
                ], 201);
            } else {
                DB::rollBack();
                return response()->json([
                    'message' => 'خطا در ویرایش متغیر'
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

    public function listingProperties(Request $request)
    {
        $data = $request->all();

        $ids = json_decode($data['ids']);
        $properties = OucProperty::whereIn('ounit_cat_id', $ids)->select('id', 'name')->get();

        $valueID = json_decode($data['valueID']);
        $propertyValues = OucPropertyValue::where('ouc_property_id', $valueID)->select('id', 'value')->get();

        return [
            'properties' => OucPropertyListResource::collection($properties),
            'propertyValues' => OucPropertyValueListResource::collection($propertyValues),
        ];
    }

    public function sectionEdit(Request $request, $circularID)
    {
        $data = $request->all();
        return response()->json($this->editSection($circularID, $data));
    }
    public function indicatorEdit(Request $request, $circularID)
    {
        $data = $request->all();
        return response()->json($this->editIndicator($circularID, $data));
    }

    public function sectionDelete( $circularID)
    {
        return response()->json($this->deleteSection($circularID));
    }
    public function indicatorDelete( $circularID)
    {
        return response()->json($this->deleteIndicator($circularID));
    }








}
