<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\LMS\app\Http\Traits\OptionTrait;
use Modules\LMS\app\Models\Option;

class OptionController extends Controller
{
    use OptionTrait;

    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        $validatedData = $request->validate([
            'questionID' => 'required|integer|exists:questions,id',
            'option' => 'required|string|max:255',
            'isCorrect' => 'required|boolean',
        ]);

        try {
            DB::beginTransaction();

            $question = $this->insertOptions([$data]);

            DB::commit();

            return response()->json(['message' => 'Success', 'data' => $question], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در افزودن گزینه', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $success = $this->deleteOption($id);

        if ($success) {
            return response()->json(['message' => 'Option has been deactivated successfully.'], 200);
        } else {
            return response()->json(['message' => 'Option not found.'], 404);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function update(array $data, Option $option): JsonResponse
    {
        $option = Option::findOrFail($data['id']);
        if ($option == null) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        try {
            DB::beginTransaction();
            $option = $this->UpdateQuestion($data, $option);
            DB::commit();
            return response()->json(['message' => 'Success', 'data' => $option], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ویرایش گزینه', 'error' => $e->getMessage()], 500);
        }
    }
}
