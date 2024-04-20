<?php

namespace Modules\FormGMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\FormGMS\app\Http\Repositories\ReportRepository;

class ReportController extends Controller
{
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
        try {
            $reportService = app()->make(ReportRepository::class);

            $report = $reportService->reportStore($data);

            if ($report instanceof \Exception) {

            }

            $reportRecordData = json_decode($data['answers'], true);

            foreach ($reportRecordData as $datum) {
                $datum['reportID']=$report->id;

                $reportRecord = $reportService->reportRecordstore($datum);

                if ($reportRecord instanceof \Exception) {

                }

            }

        } catch (BindingResolutionException $e) {
        }


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
