<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\LMS\app\Http\Traits\OptionTrait;
use Modules\LMS\app\Models\Option;
use Modules\LMS\app\Resources\OptionsResource;

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
            'title' => 'required|string|max:255',
            'isCorrect' => 'required|boolean',
        ]);

        try {
            DB::beginTransaction();

            $option = $this->insertOptions($data, $validatedData ['questionID']);

            if (!$option) {
                throw new \Exception('Failed to insert option.');
            }

            DB::commit();
            $response = OptionsResource::make($option);

            return response()->json(['message' => 'Success', 'data' => $option, 'response' => $response], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error adding option', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroyOption($id)
    {
        $success = $this->deleteOption($id);

        if ($success) {
            return response()->json(['message' => 'Question has been deactivated successfully.'], 200);
        } else {
            return response()->json(['message' => 'Question not found.'], 404);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function UpdateOption(Request $request, $id)
    {
        $option = Option::findOrFail($id);
        if ($option == null) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        try {
            DB::beginTransaction();
            $option = $this->editOption($option, $request);
            $response = OptionsResource::make($option);

            DB::commit();
            return response()->json(['message' => 'Success', 'data' => $option, 'response' => $response], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error updating option', 'error' => $e->getMessage()], 500);
        }
    }
}
