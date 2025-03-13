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
use Modules\EVAL\app\Models\EvalCircularIndicator;
use Modules\EVAL\app\Models\EvalCircularSection;
use Modules\EVAL\app\Resources\CircularFirstListResource;
use Modules\EVAL\app\Resources\ItemsListResource;
use Modules\EVAL\app\Resources\LastDataResource;
use Modules\EVAL\app\Resources\ListOfDistrictCompletedResource;
use Modules\EVAL\app\Resources\ListOfDistrictWaitToCompleteResource;
use Modules\EVAL\app\Resources\PropertiesAndvaluesResource;
use Modules\EVAL\app\Resources\SingleResource;
use Modules\HRMS\app\Http\Enums\OunitCategoryEnum;
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
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'message' => 'کاربر مورد نظر یافت نشد'
                ], 403);
            }

            DB::beginTransaction();
            $validated = $request->all();


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
        return response()->json([
            'data' => SingleResource::make($circular),
            'completedCircularCount' => $data['completedCircularCount']
        ]);
    }


    public function circularSearch(Request $request)
    {
        $data = $request->all();
        $perPage = $data['perPage'] ?? 10;
        $pageNum = $data['pageNum'] ?? 1;
        $validatedData = $request->validate([
            'name' => 'nullable|string',
        ]);

        $list = $this->CircularsList($perPage,$pageNum,$validatedData);

        if ($list->isEmpty()){
            return response()->json(['message' => 'لیستی برای جستجو یافت نشد'], 404);
        }
        else{
            return response()->json(new CircularFirstListResource($list));
        }


    }

    public function showLastCircularData($circularID)
    {
        $list = $this->lastDataForEditCircular($circularID);
        if (!$list){
            return response()->json(['message' => 'لیستی وجود ندارد'], 404);
        }
        else{
            return response()->json(new LastDataResource($list));
        }
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

            $editCircular = $this->circularEdit($circularID, $data, $user);


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
        return response()->json($this->EvaluationWaitToCompleteList($user));
    }

    public function listForDistrictWaitingAndCompletedList(Request $request)
    {
        $data = $request->all();
        $perPage = $data['perPage'] ?? 10;
        $pageNum = $data['pageNum'] ?? 1;
        $user = Auth::user();

        $data = $request->all();
        $districtList = $this->listOfDistrictWaitingAndCompletedList($perPage,$pageNum,$data,$user);
        if (!$user) {
            return response()->json([
                'message' => 'شما بخشدار هیج سازمانی نمی باشید'
            ], 403);
        }
        return response()->json(new ListOfDistrictCompletedResource( $districtList));
    }

    public function listForDistrictCompleted(Request $request)
    {
        $user = Auth::user();
        $data = $request->all();
        $perPage = $data['perPage'] ?? 10;
        $pageNum = $data['pageNum'] ?? 1;
        $districtList = $this->listOfDistrictCompletedList( $perPage , $pageNum, $data, $user);
        if (!$user) {
            return response()->json([
                'message' => 'شما بخشدار هیج سازمانی نمی باشید'
            ], 403);
        }
        return response()->json(new ListOfDistrictWaitToCompleteResource($districtList));
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
    public function dropDownsToEditVariable($variableID)
    {
        $dropDown = $this->requirementOfEditVariable($variableID);
        return response()->json($dropDown);
    }

    public function createVariable(Request $request, $circularID)
    {
        try {
            $circular = EvalCircular::find($circularID);
        if (!$circular) {
            return response()->json([
                'message' => 'بخشنامه مورد نظر یافت نشد'
            ], 404);
        }
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'message' => 'کاربر مورد نظر یافت نشد'
                ], 403);
            }

            DB::beginTransaction();
            $data = $request->all();

            $editVariable = $this->addVariable($circularID, $data);

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

    public function updateVariable(Request $request, $variableId)
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
            $editVariable = $this->editVariable($variableId, $data);


            if ($editVariable) {
                DB::commit();
                return response()->json([
                    'message' => 'متغیر با موفقیت ویرایش شد',
                    'id' => $variableId
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

    public function editVariableRequirement($variableID)
    {
        return response()->json($this->lastDataForEditVariable($variableID));
    }


    public function listingProperties($circularID)
    {
        $oUnitCatId = OunitCategoryEnum::VillageOfc->value;
        $properties = OucProperty::with('values')->where('ounit_cat_id', $oUnitCatId)->select('id', 'name')->get();

        return [
            'properties' => PropertiesAndvaluesResource::collection($properties),
            'dropdowns'=>$this->requirementOfAddVariable($circularID)
        ];
    }
    public function listingPropertiesForEdit($variableID)
    {
        $oUnitCatId = OunitCategoryEnum::VillageOfc->value;
        $properties = OucProperty::with('values')->where('ounit_cat_id', $oUnitCatId)->select('id', 'name')->get();

        return [
            'properties' => PropertiesAndvaluesResource::collection($properties),
            'dropdowns'=>$this->requirementOfEditVariable($variableID),
        ];
    }

    public function sectionEdit(Request $request, $sectionID)
    {
        try {
            $section = EvalCircularSection::find($sectionID);
            if (!$section) {
                return response()->json([
                    'message' => 'بخش مورد نظر یافت نشد'
                ], 403);
            }

            DB::beginTransaction();

            $data = $request->all();
            $edit = $this->editSection($section, $data);
            if ($edit) {
                DB::commit();
                return response()->json([
                    'message' => 'بخش با موفقیت ویرایش شد',
                    'id' => $sectionID
                ], 201);
            } else {
                DB::rollBack();
                return response()->json([
                    'message' => 'خطا در ویرایش بخش'
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

    public function indicatorEdit(Request $request, $indicatorID)
    {
        try {
            $indicator = EvalCircularIndicator::find($indicatorID);
            if (!$indicator) {
                return response()->json([
                    'message' => 'شاخص مورد نظر یافت نشد'
                ], 403);
            }
            DB::beginTransaction();

            $data = $request->all();
            $edit = $this->editIndicator($indicator, $data);
            if ($edit) {
                DB::commit();

                return response()->json([
                    'message' => 'شاخص با موفقیت ویرایش شد',
                ], 201);
            } else {
                return response()->json([
                    'message' => 'خطا در ویرایش شاخص',
                ], 500);
            }
        }catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'خطای غیرمنتظره رخ داد',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function sectionDelete($sectionID)
    {
        try {
            $section = EvalCircularSection::find($sectionID);
            if (!$section) {
                return response()->json([
                    'message' => 'بخش مورد نظر یافت نشد'
                ], 403);
            }
            DB::beginTransaction();

            $delete = $this->deleteSection($section->id);
            if ($delete) {
                DB::commit();

                return response()->json([
                    'message' => 'بخش با موفقیت حذف شد'
                ], 201);
            } else {
                return response()->json([
                    'message' => 'خطا در حذف بخش'
                ], 500);
            }
        }catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'خطای غیرمنتظره رخ داد',
                'error' => $e->getMessage(),
                ], 500);

        }
    }

    public function indicatorDelete($indicatorID)
    {
        try {

            DB::beginTransaction();

            $delete = $this->deleteIndicator($indicatorID);
            if ($delete) {
                DB::commit();
                return response()->json([
                    'message' => 'شاخص با موفقیت حذف شد'
                ], 201);
            } else {
                return response()->json([
                    'message' => 'خطا در حذف شاخص'
                ], 500);
            }
        }catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'خطای غیرمنتظره رخ داد',
                'error' => $e->getMessage(),
                ], 500);
        }
    }

    public function variableDelete($variableID)
    {
        try {

            DB::beginTransaction();

            $delete = $this->deleteVariable($variableID);
            if ($delete) {
                DB::commit();
                return response()->json([
                    'message' => 'متغیر با موفقیت حذف شد'
                ], 201);
            } else {
                return response()->json([
                    'message' => 'خطا در حذف متغیر'
                ], 500);
            }
        }catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'خطای غیرمنتظره رخ داد',
                'error' => $e->getMessage(),
            ], 500);
        }

    }


}
