<?php

namespace Modules\FormGMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery\Exception;
use Modules\FormGMS\app\Http\Repositories\FieldRepository;
use Modules\FormGMS\app\Http\Repositories\FormRepository;
use Modules\FormGMS\app\Http\Repositories\OptionRepository;
use Modules\FormGMS\app\Http\Repositories\PartRepository;
use Modules\FormGMS\app\Models\Field;
use Modules\FormGMS\app\Models\FieldType;
use Modules\FormGMS\app\Models\Form;
use Modules\FormGMS\app\Models\Option;

class FormGMSController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $forms = Form::with('status')->get();


        return response()->json($forms);
    }

    /**
     * Store a newly created resource in storage.
     */
//    public function store(Request $request): JsonResponse
//    {
//        $data = $request->all();
//
//
//        $formService = new FormRepository();
//        $data['userID'] = \Auth::user()->id;
//        $formResult = $formService->store($data);
//
//        if ($formResult instanceof \Exception) {
//            return response()->json(['message' => 'خطا در ایجاد فرم'], 500);
//        }
//        $partService = new PartRepository();
////            $partData = json_decode($data['parts'],true) ;
//        $partData['formID'] = $formResult->id;
//
//        $partResult = $partService->store($partData);
//        if ($partResult instanceof \Exception) {
//
//            return response()->json(['message' => 'خطا در ایجاد فرم'], 500);
//
//        }
//        $fieldData = json_decode($data['fields'], true);
//
//        $fieldService = new FieldRepository();
//
//        foreach ($fieldData as $item) {
//            $item['partID'] = $partResult->id;
//            $fieldResult = $fieldService->store($item);
//
//            if ($fieldResult instanceof \Exception) {
//
//                return response()->json(['message' => 'خطا در ایجاد فرم'], 500);
//
//            }
//
//            if (isset($item['options'])) {
//
//                $optionService = new OptionRepository();
//                $optionResult = $optionService->store($item['options'], $fieldResult->id);
//                if ($optionResult instanceof \Exception) {
//
//                    return response()->json(['message' => 'خطا در ایجاد فرم'], 500);
//
//                }
//            }
//
//        }
//
//        return response()->json($formResult);
//    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        $formService = new FormRepository();
        $data['userID'] = \Auth::user()->id;

        try {
            DB::beginTransaction();

            $formResult = $formService->store($data);

            $partService = new PartRepository();
            $partData['formID'] = $formResult->id;

            $partResult = $partService->store($partData);

            $fieldData = json_decode($data['fields'], true);
            $fieldCollection = collect($fieldData);

            $fieldsWithoutOptions = $fieldCollection->whereNull('options');
            $fieldsWithOptions = $fieldCollection->whereNotNull('options');

            $fieldService = new FieldRepository();

            $FWOresult = $fieldService->bulkStore($fieldsWithoutOptions, $partResult->id);

            $optionService = new OptionRepository();

            $fieldsWithOptions->each(function ($fieldData) use ($fieldService, $partResult, $optionService) {
                $fieldData['partID'] = $partResult->id;
                $field = $fieldService->store($fieldData);
                $options = $optionService->bulkStore($fieldData['options'], $field->id);
            });

            DB::commit();

            return response()->json($formResult);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ایجاد فرم'], 500);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        $form = Form::with('fields.options', 'fields.fieldType')->findOrFail($id);


        return response()->json($form);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {

        $form = Form::with('part')->findOrFail($id);
        $part = $form->part[0];
        $data = $request->all();

        try {
            DB::beginTransaction();
            $fieldData = json_decode($data['fields'], true);

            $fieldCollection = collect($fieldData);

            $fieldsWithoutOptions = $fieldCollection->whereNull('options');
            $fieldsWithOptions = $fieldCollection->whereNotNull('options');

            $fieldService = new FieldRepository();

            $fieldService->bulkUpdate($fieldsWithoutOptions, $part->id);

            $optionService = new OptionRepository();

            $fieldsWithOptions->each(function ($field) use ($fieldService, $part, $optionService) {
                $upsertField = $fieldService->update($field, $part->id);
                $optionResult = $optionService->bulkUpdate($field['options'], $upsertField->id);

            });
            $deletedFields = json_decode($data['deletedFields'],true);
            // Update the status using whereIn and update
            $deleteStatus = Field::GetAllStatuses()->where('name', '=', 'غیرفعال')->first();
            Field::whereIn('id', $deletedFields)
                ->update(['status_id' => $deleteStatus->id]);

            $deletedOptions = json_decode($data['deletedOptions'],true);
            $deleteStatus = Option::GetAllStatuses()->where('name', '=', 'غیرفعال')->first();
            Option::whereIn('id', $deletedOptions)
                ->update(['status_id' => $deleteStatus->id]);
            DB::commit();
            return response()->json(['message' => 'با موفقیت بروزرسانی شد'], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ایجاد فرم'], 500);

        }


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    public function getBaseInfo()
    {
        $result = FieldType::all();

        return response()->json($result);

    }
}
