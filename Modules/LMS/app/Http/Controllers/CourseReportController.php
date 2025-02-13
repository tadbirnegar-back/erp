<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\LMS\app\Http\Traits\CourseReportTrait;
use Modules\LMS\app\Resources\CourseReportResource;

class CourseReportController extends Controller
{
    use CourseReportTrait;

    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index($courseID): JsonResponse
    {
        $courseReport = $this->CourseInformation($courseID);
//        return response()->json($courseReport);
        return CourseReportResource::make($courseReport);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        //

        return response()->json($this->data);
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
