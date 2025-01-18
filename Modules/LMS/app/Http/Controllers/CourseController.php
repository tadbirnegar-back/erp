<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\AAA\app\Models\User;
use Modules\AddressMS\app\Models\City;
use Modules\AddressMS\app\Models\District;
use Modules\AddressMS\app\Models\State;
use Modules\HRMS\app\Http\Enums\OunitCategoryEnum;
use Modules\HRMS\app\Http\Traits\JobTrait;
use Modules\HRMS\app\Models\Job;
use Modules\HRMS\app\Models\Position;
use Modules\LMS\app\Http\Services\PurchaseCourse;
use Modules\LMS\app\Http\Services\VerificationPayment;
use Modules\LMS\app\Http\Traits\CourseCourseTrait;
use Modules\LMS\app\Http\Traits\CourseEmployeeFeatureTrait;
use Modules\LMS\app\Http\Traits\CourseTargetTrait;
use Modules\LMS\app\Http\Traits\CourseTrait;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\CourseCourse;
use Modules\LMS\app\Resources\AllCoursesListResource;
use Modules\LMS\app\Resources\CourseListResource;
use Modules\LMS\app\Resources\CourseShowForUpdateResource;
use Modules\LMS\app\Resources\CourseViewLearningResource;
use Modules\LMS\app\Resources\LessonDetailsResource;
use Modules\LMS\app\Resources\LessonListResource;
use Modules\LMS\app\Resources\LiveOunitSearchForCourseResource;
use Modules\LMS\app\Resources\MyCoursesListResource;
use Modules\LMS\app\Resources\RelatedCourseListResource;
use Modules\LMS\app\Resources\SideBarCourseShowResource;
use Modules\LMS\app\Resources\ViewCourseSideBarResource;
use Modules\OUnitMS\app\Models\CityOfc;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\StateOfc;
use Modules\OUnitMS\app\Models\TownOfc;
use Modules\OUnitMS\app\Models\VillageOfc;
use Modules\PayStream\app\Models\Online;

class CourseController extends Controller
{
    use CourseTrait, CourseCourseTrait, CourseTargetTrait, CourseEmployeeFeatureTrait , JobTrait;

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $user = Auth::user();
            //Store Course base datas
            $course = $this->storeCourseDatas($data, $user);
            //Store course Status
            $this->storePishnevisStatus($course->id);
            //store preRequisites
            if (isset($data['preRequisiteCourseIDs']) && !is_null($data['preRequisiteCourseIDs'])) {
                $this->storePreRequisite($course->id, $data['preRequisiteCourseIDs']);
            }
            //Store Target Points
            if (isset($data['courseTargets'])) {
                $this->storeCourseTarget($course->id, $data['courseTargets']);
            }
            DB::commit();
            return response()->json(['message' => "دوره با موفقیت ساخته شد"]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['message' => $exception->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $course = Course::find($id);
            $course = $this->updateCourseDatas($course, $data);
            //store preRequisites
            if (isset($data['preRequisiteCourseIDs'])) {
                $this->syncPreReqDatas($course, $data['preRequisiteCourseIDs']);
            }
            //Store Target Points
            if (isset($data['courseTargets'])) {
                $this->storeCourseTarget($course->id, $data['courseTargets']);
            }

            if (isset($data['courseTargetsIDs'])) {
                $ctIDs = json_decode($data['courseTargetsIDs']);
                $this->deleteCourseTarget($ctIDs);
            }
            DB::commit();
            return response() -> json(['message' => 'دوره شما با موفقیت به روز رسانی شد'] , 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response() -> json(['message' => $e->getMessage()] , 200);
        }


    }

    public function updateDataShow($id)
    {
        $course = Course::find($id);
        if (empty($course)) {
            return response()->json(['message' => "همچین دوره ای وجود ندارد"], 403);
        }
        $data = $this->showCourseForUpdate($id);
        return new CourseShowForUpdateResource($data);
    }

    public function show($id)
    {
        try {
            $course = Course::whereHas('latestStatus', function ($query) {
                $query->whereIn('statuses.id', [
                    $this->coursePresentingStatus()->id,
                    $this->courseEndedStatus()->id,
                    $this->courseCanceledStatus()->id
                ]);
            })->with('latestStatus')->find($id);
            $user = Auth::user();
            if (is_null($course) || empty($course->latestStatus)) {
                return response()->json(['message' => 'دوره مورد نظر یافت نشد'], 403);
            }

            $componentsToRenderWithData = $this->courseShow($course, $user);
            return response()->json($componentsToRenderWithData);
        } catch (\Exception $e) {
            return response()->json(['message' => 'دوره مورد نظر یافت نشد'], 403);
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

            $user = Auth::user();
            // Check if the user has completed prerequisite courses.
            // This is currently implemented in the simplest possible way and might be updated in the future.
            if (empty($course->prerequisiteCourses[0])) {
                $isPreDone = true;
            } else {
                $isPreDone = $this->isJoinedPreRerequisites($user, $course);
            }
            if ($isPreDone) {
                $purchase = new PurchaseCourse($course, $user);
                $response = $purchase->handle();
            } else {

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
        $online = Online::where('authority', $data['authority'])->first();
        try {
            DB::beginTransaction();
            $verify = new VerificationPayment($online);
            $result = $verify->verifyPayment();
            DB::commit();
            return response()->json($result);
        } catch (\Exception $exception) {
            DB::rollBack();
            DB::beginTransaction();
            $verify = new VerificationPayment($online);
            $result = $verify->DeclinePayment();
            DB::commit();
            return response()->json($result);
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
        $course = Course::leftJoinRelationship('chapters.lessons')->whereHas('latestStatus', function ($query) {
            $query->whereIn('statuses.id', [
                $this->coursePresentingStatus()->id,
                $this->courseEndedStatus()->id,
            ]);
        })->find($id);
        if (empty($course)) {
            return response()->json(["message" => "دوره با این مشخصات وجود ندارد"], 403);
        }
        $user = Auth::user();
        $isEnrolled = $this->isEnrolledToDefinedCourse($course->id, $user);

        //Check user is Joined or not
        if (empty($isEnrolled->isEnrolled[0])) {
            $joined = false;
        } else {
            $joined = true;
        }
        if (!$joined) {
            return response()->json(["message" => "شما دسترسی به این دوره را ندارید"], 400);
        }

        $data = $this->dataShowViewCourseSideBar($course, $user);

        $sidebar = new SideBarCourseShowResource($data);
        return response()->json($sidebar);

    }


    public function courseListAll()
    {
        $query = Course::query();

        $course = $query->select('id', 'title')->get();

        $response = AllCoursesListResource::collection($course);

        return response()->json($response);
    }


    public function liveSearchOunit(Request $request)
    {
        $data = $request->all();
        $searchTerm = $data['name'] ?? '';

        $results = OrganizationUnit::query()
            ->where('name', 'like', '%' . $searchTerm . '%')
            ->whereIn('unitable_type', [StateOfc::class, CityOfc::class, DistrictOfc::class, VillageOfc::class])
            ->take(7)
            ->with([
                'ancestors',
                'unitable'
            ])
            ->get();

        $response = LiveOunitSearchForCourseResource::collection($results);
        $jobs = Job::where('status_id' , $this->activeJobStatus()->id)->get();


        return response()->json(["category" => $response, "jobs" => $jobs]);
    }

    public function myEnrolledCourses(Request $request)
    {
        $user = Auth::user();
        $user->load('customer');
        $enrolledCourses = $this->enrolledCourses($user);
        return new MyCoursesListResource(collect($enrolledCourses));
    }

    public function relatedCoursesList(Request $request)
    {
        $user = Auth::user();
        $user->load('activeRecruitmentScripts');
        $ounits = $user->activeRecruitmentScripts
            ->pluck('organization_unit_id')
            ->filter()
            ->toArray();


        //Get All the ounits can be inside target
        $relatedOrgans = OrganizationUnit::with(['ancestors' => function ($query) use ($ounits) {
            $query->whereNot('unitable_type', TownOfc::class);
        }])->whereIn('id', $ounits)->get();

        $allData = [];

        foreach ($relatedOrgans as $unit) {
            $categoryId = OunitCategoryEnum::getValueFromlabel($unit['unitable_type']);
            if ($categoryId) {
                $allData[] = [
                    'id' => $unit['id'],
                    'category_id' => $categoryId,
                ];
            }

            foreach ($unit['ancestors'] as $ancestor) {
                $ancestorCategoryId = OunitCategoryEnum::getValueFromlabel($ancestor['unitable_type']);
                if ($ancestorCategoryId) {
                    $allData[] = [
                        'id' => $ancestor['id'],
                        'category_id' => $ancestorCategoryId,
                    ];
                }
            }
        }

// Remove duplicate entries
        $allOunits = array_unique($allData, SORT_REGULAR);

        $levels = $user->activeRecruitmentScripts
            ->pluck('level_id')
            ->filter()
            ->unique()
            ->toArray();

        $positions = $user->activeRecruitmentScripts
            ->pluck('position_id')
            ->filter()
            ->unique()
            ->toArray();

        $jobs = $user->activeRecruitmentScripts
            ->pluck('job_id')
            ->filter()
            ->unique()
            ->toArray();

        $title = $request->title;
        $courses = $this->getRelatedLists($title, $allOunits, $levels, $positions, $jobs);
        return new RelatedCourseListResource(collect($courses));
    }

}
