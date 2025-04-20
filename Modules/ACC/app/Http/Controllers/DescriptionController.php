<?php

namespace Modules\ACC\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\ACC\app\Models\DocDescription;
use Validator;

class DescriptionController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'ounitID' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        $descriptions = DocDescription::where('ounit_id', $data['ounitID'])->get(['id', 'title']);

        return response()->json($descriptions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function bulkUpsert(Request $request): JsonResponse
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'ounitID' => 'required',
            'descriptions' => ['required', 'json'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            \DB::beginTransaction();
            $descriptions = collect(json_decode($data['descriptions'], true));


            $descriptions = $descriptions->map(function ($item) use ($data) {
                return [
                    'ounit_id' => $data['ounitID'],
                    'title' => $item['title'],
                    'id' => $item['id'] ?? null,
                ];
            });

            DocDescription::upsert($descriptions->toArray(), ['id']);
            \DB::commit();

            return response()->json(['message' => 'با موفقیت اضافه شد', 'data' => $this->index($request)]);

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            \DB::beginTransaction();
            $description = DocDescription::find($id);
            if (!$description) {
                return response()->json(['message' => 'سند یافت نشد'], 404);
            }
            $description->delete();
            \DB::commit();
            return response()->json(['message' => 'با موفقیت حذف شد'], 200);

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }
}
