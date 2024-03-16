<?php

namespace Modules\FormGMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\FormGMS\app\Http\Repositories\FieldRepository;
use Modules\FormGMS\app\Http\Repositories\FormRepository;
use Modules\FormGMS\app\Http\Repositories\OptionRepository;
use Modules\FormGMS\app\Http\Repositories\PartRepository;
use Modules\FormGMS\app\Models\FieldType;
use Modules\FormGMS\app\Models\Form;

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

            $fieldsWithOptions->each(function ($fieldData) use ($fieldService, $optionService) {
                $field = $fieldService->store($fieldData);
                $options = $optionService->store($fieldData['options'], $field->id);
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

    public function getBaseInfo()
    {
        $result = FieldType::all();

        return response()->json($result);

    }
}
