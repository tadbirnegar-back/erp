<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\HRMS\app\Http\Enums\OunitCategoryEnum;
use Modules\HRMS\app\Http\Traits\JobTrait;
use Modules\HRMS\app\Models\Job;
use Modules\LMS\app\Http\Enums\LessonStatusEnum;
use Modules\LMS\app\Http\Services\PurchaseCourse;
use Modules\LMS\app\Http\Services\VerificationPayment;
use Modules\LMS\app\Http\Traits\CourseCourseTrait;
use Modules\LMS\app\Http\Traits\CourseEmployeeFeatureTrait;
use Modules\LMS\app\Http\Traits\CourseTargetTrait;
use Modules\LMS\app\Http\Traits\CourseTrait;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\StatusCourse;
use Modules\LMS\app\Resources\AllCoursesListResource;
use Modules\LMS\app\Resources\CourseListResource;
use Modules\LMS\app\Resources\CourseShowForUpdateResource;
use Modules\LMS\app\Resources\CourseViewLearningResource;
use Modules\LMS\app\Resources\LessonDetailsResource;
use Modules\LMS\app\Resources\LessonListResource;
use Modules\LMS\app\Resources\LiveOunitSearchForCourseResource;
use Modules\LMS\app\Resources\MyCoursesListResource;
use Modules\LMS\app\Resources\PublishCoursePreviewResource;
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
    use CourseTrait, CourseCourseTrait, CourseTargetTrait, CourseEmployeeFeatureTrait, JobTrait;

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
            return response()->json(['message' => 'دوره شما با موفقیت به روز رسانی شد'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 200);
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

            $componentsToRenderWithData['course']->chapters->each(function ($chapter) {
                $chapter->setRelation(
                    'lessons',
                    $chapter->lessons->filter(function ($lesson) {
                        return $lesson->lastStatus[0]->name === LessonStatusEnum::ACTIVE->value;
                    })
                );
            });


            return response()->json($componentsToRenderWithData);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }

    public function courseList(Request $request)
    {
        $data = $request->all();
        $perPage = $data['perPage'] ?? 10;
        $pageNum = $data['pageNum'] ?? 1;

        $result = $this->courseIndex($perPage, $pageNum, $data);
        return CourseListResource::collection($result);
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
        $jobs = Job::where('status_id', $this->activeJobStatus()->id)->get();


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
        $data = $request->all();
        $perPage = $data['perPage'] ?? 50;
        $pageNum = $data['pageNum'] ?? 1;

        $user->load([
            'activeRecruitmentScripts.ounit' => function ($query) {
                $query->with(['ancestorsAndSelf' => function ($q) {
                    $q->whereNot('unitable_type', TownOfc::class);
                }]);
            }
        ]);

        $villageOfcs = [];

        foreach ($user->activeRecruitmentScripts as $script) {
            if ($script->ounit && $script->ounit->unitable_type === VillageOFC::class) {
                $script->ounit->load('unitable');
                $villageOfcs[] = $script->ounit->unitable;
            }
        }


        $relatedOrgans = $user->activeRecruitmentScripts
            ->pluck('ounit.ancestorsAndSelf')
            ->flatten(1)
            ->unique()
            ->toArray();


        $allData = [];

        foreach ($relatedOrgans as $unit) {
            $ancestorCategoryId = OunitCategoryEnum::getValueFromlabel($unit['unitable_type']);
            if ($ancestorCategoryId) {
                $allData[] = [
                    'id' => $unit['id'],
                    'category_id' => $ancestorCategoryId,
                ];
            }

        }

        $allOunits = array_unique($allData, SORT_REGULAR);


        //Employee Features Plucks
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


        //Ounit features plucks
        $villageOfcs = collect($villageOfcs);
        $isTourism = $villageOfcs->pluck('isTourism')->toArray();
        $isAttachedToCity = $villageOfcs->pluck('isAttached_to_city')->toArray();
        $isFarm = $villageOfcs->pluck('isFarm')->toArray();
        $degree = $villageOfcs->pluck('degree')->toArray();

        $title = $request->name;
        $courses = $this->getRelatedLists($title, $allOunits, $levels, $positions, $jobs, $isTourism, $isFarm, $isAttachedToCity, $degree, $perPage, $pageNum);
        return RelatedCourseListResource::collection($courses);
    }
    public function publishCourseDataShow($id)
    {
        $data = $this->showCourseDataForEnteshareDore($id);
        return new PublishCoursePreviewResource($data);
    }


    public function makeCoursePublish($id)
    {
        try {
            DB::beginTransaction();
            StatusCourse::create([
                'course_id' => $id,
                'status_id' => $this->courseWaitPresentingStatus()->id,
                'create_date' => now()
            ]);
            StatusCourse::create([
                'course_id' => $id,
                'status_id' => $this->coursePresentingStatus()->id,
                'create_date' => now()
            ]);
            Course::find($id)->update([
                'access_date' => now()
            ]);
            DB::commit();
            return response()->json(['message' => "دوره با موفقیت منتشر شد"]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(["message" => $exception->getMessage()], 400);
        }

    }

    public function deleteCourse($id)
    {
        try {
            DB::beginTransaction();
            StatusCourse::create([
                'course_id' => $id,
                'status_id' => $this->courseDeletedStatus()->id,
                'create_date' => now()
            ]);
            DB::commit();
            return response()->json(['message' => "دوره با موفقیت حذف شد"]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(["message" => $exception->getMessage()], 400);
        }
    }

    public function cancelCourse(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            StatusCourse::create([
                'course_id' => $id,
                'status_id' => $this->courseCanceledStatus()->id,
                'create_date' => now(),
                'description' => $request->description
            ]);
            DB::commit();
            return response()->json(['message' => "دوره با موفقیت حذف شد"]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(["message" => $exception->getMessage()], 400);
        }
    }

}
