<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\AAA\app\Models\User;
use Modules\LMS\app\Http\Enums\LessonStatusEnum;
use Modules\LMS\app\Http\Services\PurchaseCourse;
use Modules\LMS\App\Http\Services\VerificationPayment;
use Modules\LMS\app\Http\Traits\CourseTrait;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Resources\CourseListResource;
use Modules\LMS\app\Resources\LessonListResource;
use Modules\PayStream\app\Models\Online;

class CourseController extends Controller
{
    use CourseTrait;
    public function show($id)
    {
        try {
            DB::beginTransaction();
            $course = Course::with('latestStatus')->findOrFail($id);
            $user = Auth::user();
            if (is_null($course)) {
                return response()->json(['message' => 'دوره مورد نظر یافت نشد'], 404);
            }

            $componentsToRenderWithData = $this->courseShow($course, $user);
            DB::commit();
            return response()->json($componentsToRenderWithData);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => "اطلاعات دربافت نشد"], 500);
        }
    }

    public function courseList(Request $request)
    {
        $data = $request->all();
        $perPage = $data['perPage'] ?? 10;
        $pageNum = $data['pageNum'] ?? 1;

        $result = $this->courseIndex($perPage, $pageNum, $data);

        $response = new CourseListResource($result);

        return $response;
    }


    public function registerCourse($id)
    {
        try {
            DB::beginTransaction();

            $course = Course::with('prerequisiteCourses')->find($id);

//            $user = Auth::user();
            $user = User::find(2174);
            // Check if the user has completed prerequisite courses.
            // This is currently implemented in the simplest possible way and might be updated in the future.
            if(empty($course->prerequisiteCourses[0])){
                $isPreDone = true;
            }else{
                $isPreDone = $this->isJoinedPreRerequisites($user, $course);
            }
            if($isPreDone){
                $purchase = new PurchaseCourse($course, $user);
                $response = $purchase->handle();
            }else{

                return response()->json([
                    'success' => false,
                    'message' => 'پیش نیاز های دوره مطالعه نشده است',
                ], 400);
            }


            DB::commit();

            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
            ], 500);
        }
    }

    public function checkPayment(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'authority' => [
                'required',
                'exists:onlines,authority'
            ]
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $online = Online::where('authority' , $data['authority'])->first();
        try {
            DB::beginTransaction();
            $verify = new VerificationPayment($online);
            $result = $verify -> verifyPayment();
            DB::commit();
            return response() -> json($result);
        }catch (\Exception $exception){
            DB::rollBack();
            DB::beginTransaction();
            $verify = new VerificationPayment($online);
            $result = $verify -> DeclinePayment();
            DB::commit();
            return response() -> json($result);
        }

    }

    public function lessonList(Request $request)
    {


        $data = $request->all();
        $perPage = $data['perPage'] ?? 10;
        $pageNum = $data['pageNum'] ?? 1;

        $result = $this->lessonIndex($perPage, $pageNum, $data);
        $response = new LessonListResource($result);

        return $response;


    }

    public function learningShow($id)
    {
        $course = Course::joinRelationshipUsingAlias('chapters.lessons')->find($id);
//        $user = Auth::user();
        $user = User::find(2174);
        $isEnrolled = $this->isEnrolledToDefinedCourse($course->id, $user);

        //Check user is Joined or not
        if(empty($isEnrolled->isEnrolled[0])){
            $joined = false;
        }else{
            $joined = true;
        }
        if(!$joined){
            return response()->json(["message" => "شما دسترسی به این دوره را ندارید"], 400);
        }

        return response()->json($course);
    }

}
