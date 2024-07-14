<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HRMS\app\Http\Traits\JobTrait;
use Modules\HRMS\app\Models\Job;

class JobController extends Controller
{
use JobTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $result = $this->getListOfJobs();

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            \DB::beginTransaction();

            // Retrieve all data from the request
            $data = $request->all();


            $job = $this->createJob($data);

            \DB::commit();

            return response()->json( $job);
        } catch (\Exception $e) {
            \DB::rollBack();

            // Return an error response
            return response()->json(['message' => 'خطا در ایجاد شغل', 'error' => $e->getMessage()], 500);
        }
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
        try {
            \DB::beginTransaction();

            // Retrieve all data from the request
            $data = $request->all();
$job = Job::findOr($id,function (){
                return response()->json(['message'=>'موزدی یافت نشد'],404);
            });
            // Fetch the job using the provided ID and update it
            $job = $this->updateJob($job, $data);

            \DB::commit();

            // Return a success response
            return response()->json($job);
        } catch (\Exception $e) {
            \DB::rollBack();

            // Return an error response
            return response()->json(['message' => 'خطا در بروزرسانی شغل', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $job = Job::findOr($id,function (){
            return response()->json(['message'=>'موزدی یافت نشد'],404);});
            $job = $this->deleteJob($job);
        return response()->json(['message' => ' با موفقیت حذف شد']);
    }
}
