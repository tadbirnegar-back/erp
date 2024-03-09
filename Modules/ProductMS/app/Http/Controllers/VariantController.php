<?php

namespace Modules\ProductMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\ProductMS\app\Http\Services\VariantGroupService;
use Modules\ProductMS\app\Http\Services\VariantService;
use Modules\ProductMS\app\Models\Variant;
use Modules\ProductMS\app\Models\VariantGroup;

class VariantController extends Controller
{
    protected VariantGroupService $variantGroupService;
    protected VariantService $variantService;
    public array $data = [];

    /**
     * @param VariantGroupService $variantGroupService
     * @param VariantService $variantService
     */
    public function __construct(VariantGroupService $variantGroupService, VariantService $variantService)
    {
        $this->variantGroupService = $variantGroupService;
        $this->variantService = $variantService;
    }


    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $variantGroups = $this->variantGroupService->index();

        return response()->json($variantGroups);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->all();

        $vGroupStatus = VariantGroup::GetAllStatuses()->where('name', '=', 'فعال')->first();
        $vStatus = Variant::GetAllStatuses()->where('name', '=', 'فعال')->first();


        $data['groupStatusID'] = $vGroupStatus->id;

        $vGroup = $this->variantGroupService->store($data);

        if ($vGroup instanceof \Exception) {
            return response()->json(['message' => 'خطا در وارد کردن متغیر جدید'], 500);

        }
        $vars = json_decode($data['variants'], true);

        $varData['variantStatusID'] = $vStatus->id;
        $varData['variantGroupID'] = $vGroup->id;

        foreach ($vars as $var) {
            $varData['variantName'] = $var['variantName'];
            $insertedVar = $this->variantService->store($varData);

            if ($insertedVar instanceof \Exception) {
                return response()->json(['message' => 'خطا در وارد کردن متغیر جدید'], 500);
            }
        }

        return response()->json($vGroup);
    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        $variantGroup = $this->variantGroupService->show($id);

        return response()->json($variantGroup);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
//        $vGroup = VariantGroup::findOrFail($id);
//        if (is_null($vGroup)) {
//            return
//        }
        $data = $request->all();
        $vGroup = $this->variantGroupService->update($data, $id);

        $variants = json_decode($data['variants'],true);
        $vStatus = Variant::GetAllStatuses()->where('name', '=', 'فعال')->first();


        if (isset($data['deleted'])) {

            $deletes = json_decode($data['deleted'], true);

            foreach ($deletes as $delete) {
                $deleteResult = $this->variantService->destroy($delete['id']);
            }
        }
        foreach ($variants as $variant) {
            if (isset($variant['id'])) {

                $vUpdate = $this->variantService->update($variant,$variant['id']);

                if ($vUpdate instanceof \Exception) {
                    return response()->json(['message'=>$vUpdate->getMessage()],500);
                }

            }else{

                $variant['variantStatusID'] = $vStatus->id;
                $variant['variantGroupID'] = $vGroup->id;

                $insert = $this->variantService->store($variant);
            }
        }
        return response()->json(['message'=>'done']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $vGroup = VariantGroup::with('variants')->findOrFail($id);
        $vGroupStatus = VariantGroup::GetAllStatuses()->where('name', '=', 'غیرفعال')->first();
        $vStatus = Variant::GetAllStatuses()->where('name', '=', 'غیرفعال')->first();

        $vGroup->status_id = $vGroupStatus->id;
        $vGroup->save();

        foreach ($vGroup->variants as $variant) {
            $variant->status_id = $vStatus->id;
            $variant->save();
        }

        return response()->json(['message' => 'حذف شد']);
    }
}
