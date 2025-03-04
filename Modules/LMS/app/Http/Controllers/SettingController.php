<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\LMS\app\Http\Traits\SettingTrait;

class SettingController extends Controller
{
    use SettingTrait;

    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $dropDowns = $this->showDropDowns();

        return response()->json($dropDowns);
    }

    public function indexComprehensive(): JsonResponse
    {
        $dropDowns = $this->showDropDownsComprehensive();
        return response()->json($dropDowns);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'Difficulty' => 'required|integer',
            'questionType' => 'required|integer',
            'questionNumber' => 'required|integer',
            'timePerQuestion' => 'required|integer',
            'passScore' => 'required|integer',
        ]);

        try {
            DB::beginTransaction();

            $insert = $this->dataToInsert($validatedData);
            DB::commit();
            return response()->json([
                'message' => 'Settings saved successfully!',
                'data' => $insert,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to save settings.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function storeComprehensive(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'Difficulty' => 'required|integer',
            'questionType' => 'required|integer',
            'questionNumber' => 'required|integer',
            'timePerQuestion' => 'required|integer',
            'passScore' => 'required|integer',
        ]);

        try {
            DB::beginTransaction();

            $insert = $this->dataToInsertComprehensive($validatedData);
            DB::commit();
            return response()->json([
                'message' => 'Settings saved successfully!',
                'data' => $insert,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to save settings.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the specified resource.
     */
    public function LastShow(): JsonResponse
    {
        $trait = $this->LastSettingShow();
        return response()->json($trait);
    }

    public function LastShowComprehensive(): JsonResponse
    {
        $trait = $this->LastSettingShowComprehensive();
        return response()->json($trait);
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
}
