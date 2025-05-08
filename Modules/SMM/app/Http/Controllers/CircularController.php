<?php

namespace Modules\SMM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\SMM\app\Models\Circular;
use Modules\SMM\app\Resources\SmmCircularListResource;
use Modules\SMM\app\Resources\SmmCircularShowResource;
use Modules\SMM\app\Traits\CircularTrait;
use Validator;

class CircularController extends Controller
{
    use CircularTrait;


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string',
            'perPage' => 'sometimes|numeric',
            'pageNum' => 'sometimes|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        $perPage = $request->get('perPage') ?? 10;
        $pageNum = $request->get('pageNum') ?? 1;
        $searchTerm = $request->get('title') ?? null;

        $index = Circular::joinRelationship('fiscalYear')
            ->latestStatus()
            ->select([
                'smm_circulars.id',
                'smm_circulars.title',
                'statuses.name as status_name',
                'statuses.class_name as status_class_name',
                'smmCircular_status.create_date as status_create_date',
                'fiscal_years.name as fiscal_year_name',

            ])
            ->when($searchTerm, function ($query) use ($searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->whereRaw('MATCH(title) AGAINST(? IN BOOLEAN MODE)', [$searchTerm]);
                });
            })
            ->orderByDesc('smmCirculars.id')
            ->paginate($perPage, ['*'], 'page', $pageNum);

        return SmmCircularListResource::collection($index);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'title' => 'required',
            'description' => 'nullable',
            'fileID' => 'required',
            'fiscalYearID' => 'required',
            'minWage' => 'sometimes|numeric',
            'marriageBenefit' => 'sometimes|numeric',
            'childBenefit' => 'sometimes|numeric',
            'rentBenefit' => 'sometimes|numeric',
            'groceryBenefit' => 'sometimes|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        try {
            DB::beginTransaction();
            $result = $this->storeSmmCircular($data);
            DB::commit();
            return response()->json(['data' => $result->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'errors' => $e->getMessage(),
            ], 500);
        }

    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        $circular = Circular::joinRelationship('fiscalYear')
            ->joinRelationship('file')
            ->latestStatus()
            ->addSelect([
                'statuses.name as status_name',
                'statuses.class_name as status_class_name',
                'smmCircular_status.create_date as status_create_date',
                'fiscal_years.name as fiscal_year_name',
                'files.name as file_name',
                'files.slug as file_slug',
                'files.size as file_size',
            ])
            ->findOrFail($id);

        if (is_null($circular)) {
            return response()->json([
                'message' => 'بخشنامه ای با این مشخصات یافت نشد',
            ], 404);
        }


        return SmmCircularShowResource::make($circular);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateCircularBase(Request $request, $id): JsonResponse
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'title' => 'required',
            'description' => 'nullable',
            'fileID' => 'required',
            'fiscalYearID' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $circular = Circular::findOrFail($id);
        if (is_null($circular)) {
            return response()->json([
                'message' => 'بخشنامه ای با این مشخصات یافت نشد',
            ], 404);
        }
        try {
            DB::beginTransaction();
            $circular->title = $data['title'];
            $circular->description = $data['description'];
            $circular->file_id = $data['fileID'];
            $circular->fiscal_year_id = $data['fiscalYearID'];
            $circular->save();
            DB::commit();
            return response()->json(['data' => $circular]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'errors' => $e->getMessage(),
            ], 500);
        }

    }

    public function updateCircularBenefits(Request $request, $id): JsonResponse
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'minWage' => 'required|numeric',
            'marriageBenefit' => 'required|numeric',
            'childBenefit' => 'required|numeric',
            'rentBenefit' => 'required|numeric',
            'groceryBenefit' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $circular = Circular::findOrFail($id);
        if (is_null($circular)) {
            return response()->json([
                'message' => 'بخشنامه ای با این مشخصات یافت نشد',
            ], 404);
        }
        try {
            DB::beginTransaction();

            $data['title'] = $circular->title;
            $data['description'] = $circular->description;
            $data['fileID'] = $circular->file_id;
            $data['fiscalYearID'] = $circular->fiscal_year_id;
            $this->updateSmmCircular($data, $circular);

            DB::commit();

            return response()->json(['data' => $circular]);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage(),
            ], 500);
        }

    }

    public function dispatchCircular(Request $request, $id): JsonResponse
    {
        try {
            $circular = Circular::findOrFail($id);
            if (is_null($circular)) {
                return response()->json([
                    'message' => 'بخشنامه ای با این مشخصات یافت نشد',
                ], 404);
            }
            DB::beginTransaction();
            $status = $this->publishCircularStatus();
            $this->attachStatusToSmmCircular($circular, $status, Auth::user()->id);
            DB::commit();
            return response()->json(['message' => 'بخشنامه مورد نظر ابلاغ شد']);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage(),
            ], 500);
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
}
