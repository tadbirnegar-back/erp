<?php

namespace Modules\LMS\app\Http\Traits;

use Illuminate\Support\Facades\DB;
use Modules\HRMS\app\Models\Job;
use Modules\HRMS\app\Models\Level;
use Modules\HRMS\app\Models\Position;
use Modules\LMS\app\Http\Enums\AnswerSheetStatusEnum;
use Modules\LMS\app\Http\Enums\CourseStatusEnum;
use Modules\LMS\app\Http\Enums\LessonStatusEnum;
use Modules\LMS\app\Models\AnswerSheet;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\StatusCourse;

trait CourseTrait
{
    use AnswerSheetTrait, LessonTrait;

    private static string $presenting = CourseStatusEnum::PRESENTING->value;
    private static string $ended = CourseStatusEnum::ENDED->value;
    private static string $canceled = CourseStatusEnum::CANCELED->value;
    private static string $deleted = CourseStatusEnum::DELETED->value;
    private static string $pishnevis = CourseStatusEnum::PISHNEVIS->value;
    private static string $bargozarShavande = CourseStatusEnum::ORGANIZER->value;
    private static string $waitToPresent = CourseStatusEnum::WAITING_TO_PRESENT->value;


    public function courseIndex(int $perPage = 10, int $pageNumber = 1, array $data = [])
    {
        $searchTerm = $data['name'] ?? null;

        $query = Course::joinRelationship('cover')
            ->addSelect([
                'courses.id',
                'courses.title',
                'courses.cover_id',
                'files.slug as cover_slug',
            ])
            ->whereHas('statusCourse.status', function ($query) {
                $query->whereIn('name', [
                    $this::$presenting,
                    $this::$pishnevis,
                    $this::$waitToPresent,
                ]);
            })
            ->with(['statusCourse.status']);

        $query->withCount(['chapters', 'lessons', 'questions']);


        $query
            ->when($searchTerm, function ($query) use ($searchTerm) {
                $query->whereRaw('MATCH(courses.title) AGAINST(?)', [$searchTerm])
                    ->orWhere('courses.title', 'LIKE', '%' . $searchTerm . '%');
            });

        $query->when($searchTerm, function ($query, $searchTerm) {
            $query->where('courses.title', 'like', '%' . $searchTerm . '%')
                ->whereRaw("MATCH (title) AGAINST (? IN BOOLEAN MODE)", [$searchTerm]);
        });
        return $query->paginate($perPage, ['*'], 'page', $pageNumber);
    }

    public function lessonIndex(int $perPage = 10, int $pageNumber = 1, array $data = [])
    {
        $searchTerm = $data['name'] ?? null;

        $courseQuery = Course::joinRelationship('cover')
            ->when($searchTerm, function ($query, $searchTerm) {
                $query->where(function ($subQuery) use ($searchTerm) {
                    $subQuery->where('courses.title', 'like', '%' . $searchTerm . '%')
                        ->orWhereRaw("MATCH (courses.title) AGAINST (? IN BOOLEAN MODE)", [$searchTerm]);
                });
            })
            ->addSelect([
                'courses.id as course_id',
                'courses.title as course_title',
                'files.slug',

            ])
            ->with(['latestStatus'])
            ->withCount([
                'chapters',
                'lessons',
                'questions',
            ])
            ->paginate($perPage, ['*'], 'page', $pageNumber);
        return $courseQuery;
    }

    public function storeCourseDatas($data, $user)
    {
        return Course::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'privacy_id' => $data['privacyID'],
            'is_required' => $data['isRequired'],
            'access_date' => isset($data['accessDate']) ? convertPersianToGregorianBothHaveTimeAndDont($data['accessDate']) : null,
            'expiration_date' => isset($data['expireDate']) ? convertPersianToGregorianBothHaveTimeAndDont($data['expireDate']) : null,
            'cover_id' => $data['coverID'],
            'preview_video_id' => $data['previewVideoID'],
            'price' => $data['price'] ?? 0,
            'creator_id' => $user->id,
            'created_date' => now()
        ]);
    }


    public function storePishnevisStatus($id)
    {
        $pishnevis = $this->coursePishnevisStatus()->id;
        StatusCourse::create([
            'course_id' => $id,
            'status_id' => $pishnevis,
            'create_date' => now()
        ]);
    }

    public function updateCourseDatas($course, $data): Course
    {

        $course->title = $data['title'] ?? $course->title;
        $course->description = $data['description'] ?? $course->description;
        $course->privacy_id = $data['privacyID'] ?? $course->privacy_id;
        $course->is_required = $data['isRequired'] ?? $course->is_required;
        $course->access_date = $data['accessDate'] ?? $course->access_date;
        $course->expiration_date = $data['expireDate'] ?? $course->expiration_date;
        $course->preview_video_id = $data['previewVideoID'] ?? $course->preview_video_id;
        $course->cover_id = $data['coverID'] ?? $course->cover_id;
        $course->save();
        return $course;
    }

    public function courseShow($course, $user)
    {
        //Take User Initial Info
        $user->load([
            'answerSheets',
            'student',
            'person.avatar'
        ]);

        $isEnrolled = $this->isEnrolledToDefinedCourse($course->id, $user);

        $answerSheet = $user->answerSheets[0] ?? null; // Handle potential null
        $student = $user->student;

        $AllowToDos = [
            "canRead" => false,
            "canTrainingExam" => false,
            "canReportCard" => false,
            "canFinalExam" => false,
            "canDegree" => false,
            "joined" => false
        ];


// Check exam approval status
        $exampApprovedStatus = $this->answerSheetApprovedStatus()->id;
        $isApproveFromExam = ($answerSheet && $answerSheet->status_id == $exampApprovedStatus);

// Check enrollment status

        if (empty($isEnrolled->isEnrolled[0])) {
            $isJoined = false;
        } else {
            $isJoined = true;
        }

        $status = $course->load('latestStatus');
        $statusName = $status->latestStatus->name;


        $myPermissions = $this->getComponentToRenderLMS($isJoined, $statusName, $isApproveFromExam);

        //Components to render with load from course
        $componentsToRender = collect([
            'MainCourse' => [
                'latestStatus',
                'cover',
                'video',
                'privacy',
                'prerequisiteCourses' => function ($query) {
                    $query->with('cover');
                },
                'chapters' => function ($query) {
                    $query->with([
                        'lessons' => function ($query) {
                            $query->whereHas('latestStatus', function ($q) {
                                $q->where('name', LessonStatusEnum::ACTIVE->value);
                            });
                        },
                    ]);
                },
            ],
            'StudyLog' => ['lessonStudyLog' => function ($query) use ($user, $isEnrolled) {
                $query->where('student_id', $user->student->id)
                    ->where('is_completed', true)
                    ->where('study_count', '<=', ($isEnrolled?->isEnrolled[0]->orderable->study_count ?? 0) + 1);

            }]
        ]);

// Flatten and prepare the relations array
        $flattenedComponents = $componentsToRender->only($myPermissions->intersect($componentsToRender->keys())->toArray())
            ->flatMap(fn($relations) => collect($relations)->mapWithKeys(fn($relation, $key) => is_callable($relation) ? [$key => $relation] : [$relation => fn($query) => $query]))->all();


// Now use the flattened components with the load method (ensure it's passed as an array)
        $course = $course->load($flattenedComponents);  // Use $flattenedComponents here
//        return $enactment;
        $componentsWithData = $componentsToRender->only($myPermissions->intersect($componentsToRender->keys()))->map(function ($relations, $component) use ($course, $user) {
            $relationData = collect($relations)->mapWithKeys(function ($relation, $key) use ($course, $user) {
                $relationName = is_callable($relation) ? explode('.', $key)[0] : explode('.', $relation)[0];

                if ($course->relationLoaded($relationName)) {
                    if (is_callable($relation)) {
                        $component = $key;
                    } else {
                        $component = $relation;
                    }
                    $result = [$component => $course->$relationName];


                    return $result;
                }
                return [];

            });

            return $relationData->isNotEmpty() ? [
                'name' => $component,
                'data' => $relationData,
            ] : null;
        })->filter()->values();

        //Buttons To render if Joined
        if ($isJoined) {
            $AllowToDos['joined'] = true;
            $AdditionalData["percentage"] = $this->calculateLessonCompletion($componentsWithData);

            if ($course->latestStatus->name == $this::$presenting) {
                $AllowToDos['canRead'] = true;
                $AllowToDos['joined'] = true;
                $AllowToDos['canTrainingExam'] = true;
                $AllowToDos['canReportCard'] = true;

                if ($AdditionalData["percentage"]["completion_percentage"] == 100) {
                    $AllowToDos["canFinalExam"] = true;
                }

                if ($isApproveFromExam) {
                    $AllowToDos["canFinalExam"] = false;
                    $AllowToDos['canDegree'] = true;
                }

            }

            if ($course->latestStatus->name == $this::$ended) {
                $AllowToDos['canRead'] = true;
                $AllowToDos['canReportCard'] = true;
                $AllowToDos['canDegree'] = true;
            }

            if ($course->latestStatus->name == $this::$canceled) {
                //
            }

            $AdditionalData["enrolled"] = $isEnrolled;
        }

        return ["course" => $course, "componentsInfo" => $componentsWithData, "usersInfo" => $user, "Permissons" => $AllowToDos, "AdditionalData" => $AdditionalData ?? null];
    }


    private function calculateLessonCompletion($response)
    {
        $totalLessons = 0;
        $completedLessons = 0;
        $allLessons = [];
        $completedLessonIds = [];
        // Extract lessons from MainCourse
        foreach ($response as $component) {
            if ($component['name'] === 'MainCourse') {
                $chapters = $component['data']['chapters'] ?? [];
                foreach ($chapters as $chapter) {
                    if (isset($chapter['lessons']) && !empty($chapter['lessons'])) {
                        foreach ($chapter['lessons'] as $lesson) {
                            $allLessons[] = $lesson['id'];
                        }
                    }
                }
            }
        }

        $totalLessons = count($allLessons);

        // Extract completed lessons from StudyLog
        foreach ($response as $component) {
            if ($component['name'] === 'StudyLog') {
                $lessonStudyLog = $component['data']['lessonStudyLog'] ?? [];
                foreach ($lessonStudyLog as $log) {
                    if ($log['is_completed'] == 1) {
                        $completedLessonIds[] = $log['lesson_id'];
                    }
                }
            }
        }

        $completedLessons = count(array_intersect($allLessons, $completedLessonIds));

        // Calculate completion percentage
        $completionPercentage = ($totalLessons > 0) ? ($completedLessons / $totalLessons) * 100 : 0;

        // Return the results
        return [
            'total_lessons' => $totalLessons,
            'completed_lessons' => $completedLessons,
            'completion_percentage' => round($completionPercentage, 2) // Round to 2 decimal places
        ];
    }

    private function getComponentToRenderLMS($isJoined, $status, $isApproveFromExam)
    {
        $statusCollection = collect($this->getByJoinAndStatusAndApproveCombination($isJoined, $status, $isApproveFromExam));
        $filtreStatus = collect($statusCollection->get($status));

        $joined = intval($isJoined);
        $filtreJoin = collect($filtreStatus->get($joined));


        $approveFromExam = intval($isApproveFromExam);
        $filterApproveFromExam = collect($filtreJoin->get($approveFromExam));

        return $filterApproveFromExam;
    }


    private function getByJoinAndStatusAndApproveCombination()
    {
        $combo = [
            $this::$canceled => [
                true => [ // this is for person is join or not
                    true => [ // this is for person has permission to get degree or not
                        "MainCourse",
                        "StudyLog"
                    ],
                    false => [
                        "MainCourse",
                        "StudyLog"
                    ]
                ],
                false => [
                    true => [
                        "MainCourse"
                    ],
                    false => [
                        "MainCourse"
                    ]
                ]
            ],
            $this::$ended => [
                true => [ // this is for person is join or not
                    true => [ // this is for person has permission to get degree or not
                        "MainCourse",
                        "StudyLog"
                    ],
                    false => [
                        "MainCourse",
                        "StudyLog"
                    ]
                ],
                false => [
                    true => [
                        "MainCourse"
                    ],
                    false => [
                        "MainCourse"
                    ]
                ]
            ],
            $this::$presenting => [
                true => [ // this is for person is join or not
                    true => [ // this is for person has permission to get degree or not
                        "MainCourse",
                        "StudyLog"
                    ],
                    false => [
                        "MainCourse",
                        "StudyLog"
                    ]
                ],
                false => [
                    true => [
                        "MainCourse"
                    ],
                    false => [
                        "MainCourse"
                    ]
                ]
            ]
        ];
        return $combo;
    }

    public function isEnrolledToDefinedCourse($courseId, $user)
    {
        $user->load(['isEnrolled' => function ($q) use ($courseId) {
            $q->whereHas('orderable', function ($query) use ($courseId) {
                $query->where('course_id', $courseId);
            });
            $q->with('orderable');
        }]);
        return $user;
    }


    public function isJoinedPreRerequisites($user, $course)
    {
        $preCoursesIds = $course->prerequisiteCourses->pluck('id')->toArray();

        $enrolls = [];
        foreach ($preCoursesIds as $preCourseId) {
            $user->load(['enrolls' => function ($q) use ($preCourseId) {
                $q->where('course_id', $preCourseId);
            }]);
            if (!empty($user->enrolls[0])) {
                $enrolls[] = true;
            } else {
                $enrolls[] = false;
            }
        }

        if (in_array(false, $enrolls, true)) {
            return false;
        } else {
            return true;
        }

    }


    public function dataShowViewCourseSideBar($course, $user)
    {
        $user->load('student');
        $data = Course::query()
            ->leftJoinRelationshipUsingAlias('chapters', 'chapters_alias')
            ->leftJoinRelationship('chapters.lessons', [
                'lessons' => fn($join) => $join->as('lesson_alias'),
                'chapters' => fn($join) => $join->on('chapters.id', 'chapters_alias.id')
            ])
            ->leftJoinRelationship('chapters.lessons.contents.file', [
                'file' => fn($join) => $join->as('file_alias'),
                'lessons' => fn($join) => $join->on('lessons.id', 'lesson_alias.id')
            ])
            ->leftJoinRelationship('chapters.lessons.lessonStudyLog', [
                'lessonStudyLog' => fn($join) => $join->as('lessonStudyLog_alias')->where('student_id', $user->student->id),
                'lessons' => fn($join) => $join->on('lessons.id', 'lesson_alias.id'),
                'chapters' => fn($join) => $join->on('chapters.id', 'chapters_alias.id'),
            ])
            ->select([
                'chapters_alias.id as chapter_id',
                'chapters_alias.description as chapter_description',
                'chapters_alias.title as chapter_title',
                'lesson_alias.id as lesson_id',
                'lesson_alias.title as lesson_title',
                'chapters_alias.id as chapter_id',
                'file_alias.duration as files_duration',
                'lessonStudyLog_alias.is_completed as is_completed',
            ])
            ->where('courses.id', $course->id)
            ->get();

        $groupedData = $data->groupBy('chapter_id')->map(function ($chapter) {
            if ($chapter->isNotEmpty()) {
                return [
                    'id' => $chapter->first()->chapter_id,
                    'title' => $chapter->first()->chapter_title,
                    'description' => $chapter->first()->chapter_description,
                    'lessons' => $chapter->groupBy('lesson_id')->map(function ($lesson) {
                        return [
                            'id' => $lesson->first()->lesson_id,
                            'title' => $lesson->first()->lesson_title,
                            'isComplete' => $lesson->first()->is_completed,
                            'duration' => convertSecondToMinute($lesson->first()->files_duration),
                            'chapter_id' => $lesson->first()->chapter_id,
                        ];
                    })->values(),
                ];
            }
            return null;
        })->filter()->values();


        //Get those with is complete of 0
        $lessonsWithIncomplete = collect($groupedData)
            ->flatMap(fn($chapter) => $chapter['lessons'])
            ->filter(fn($lesson) => $lesson['isComplete'] === 0)
            ->pluck('id');

        if ($lessonsWithIncomplete->isNotEmpty()) {
            $lastLessonId = $lessonsWithIncomplete->last();
        } else {

            $lastLessonId = collect($groupedData)
                ->flatMap(fn($chapter) => $chapter['lessons'])
                ->pluck('id')
                ->first();
        }

        return [
            "lessonID" => $lastLessonId,
            "sidebar" => $data,
        ];
    }

    public function showCourseForUpdate($id)
    {
        $query = Course::query()
            ->leftJoinRelationshipUsingAlias('video', 'course_video_alias')
            ->leftJoinRelationshipUsingAlias('cover', 'course_cover_alias')
            ->leftJoinRelationship('preReqForJoin.preReqCourse', [
                'preReqForJoin' => fn($join) => $join->as('pre_req_pivot_alias')
                    ->on('pre_req_pivot_alias.main_course_id', 'courses.id'),
                'preReqCourse' => fn($join) => $join->as('pre_reg_alias')
                    ->on('pre_reg_alias.id', 'pre_req_pivot_alias.prerequisite_course_id'),
            ])
            ->leftJoin('course_targets as course_target_alias', 'course_target_alias.course_id', 'courses.id')
            ->leftJoin('course_employees_features as course_employee_alias', 'course_employee_alias.course_target_id', 'course_target_alias.id')
            ->leftJoin('organization_units as ounit_alias', 'course_target_alias.parent_ounit_id', 'ounit_alias.id')
            // Join Levels
            ->leftJoin('levels as level_alias', function ($join) {
                $join->on('course_employee_alias.propertyble_id', '=', 'level_alias.id')
                    ->where('course_employee_alias.propertyble_type', '=', DB::raw("'" . addslashes(Level::class) . "'"));
            })

            // Join Jobs
            ->leftJoin('jobs as job_alias', function ($join) {
                $join->on('course_employee_alias.propertyble_id', '=', 'job_alias.id')
                    ->where('course_employee_alias.propertyble_type', '=', DB::raw("'" . addslashes(Job::class) . "'"));
            })

            // Join Positions
            ->leftJoin('positions as position_alias', function ($join) {
                $join->on('course_employee_alias.propertyble_id', '=', 'position_alias.id')
                    ->where('course_employee_alias.propertyble_type', '=', DB::raw("'" . addslashes(Position::class) . "'"));
            })
            ->leftJoinRelationship('courseTarget.ounitFeatures.value.oucProperty', [
                'ounitFeatures' => fn($join) => $join->as('ounit_feature_alias'),
                'value' => fn($join) => $join->as('value_alias'),
                'oucProperty' => fn($join) => $join->as('oucProperty'),
            ])
            ->select([
                'courses.id as course_alias_id',
                'courses.title as course_alias_title',
                'courses.description as course_alias_description',
                'courses.is_required as course_alias_is_required',
                'courses.expiration_date as course_alias_expiration_date',
                'courses.access_date as course_alias_access_date',
                'courses.privacy_id as course_alias_privacy_id',
                'course_video_alias.slug as course_video_slug',
                'course_video_alias.name as course_video_title',
                'course_cover_alias.id as course_video_id',
                'course_cover_alias.slug as course_cover_slug',
                'course_cover_alias.name as course_cover_title',
                'course_video_alias.id as course_cover_id',
                'pre_reg_alias.id as pre_reg_alias_id',
                'pre_reg_alias.title as pre_reg_alias_title',
                'course_target_alias.id as course_target_id',
                'ounit_alias.name as ounit_alias_name',
                'course_employee_alias.propertyble_type as course_employee_alias_propertyble_type',
                'ounit_feature_alias.id as ounit_feature_alias_id',
                'level_alias.name as level_alias_name',
                'job_alias.title as job_alias_title',
                'position_alias.name as position_alias_name',
                'value_alias.value as value_alias_value',
                'value_alias.operator as value_alias_operator',
                'oucProperty.name as oucProperty_name',
                'oucProperty.id as oucProperty_id',
                'oucProperty.ounit_cat_id as ounit_category_id'

            ])->where('courses.id', $id)->get();
        return $query;
    }

    public function coursePresentingStatus()
    {
        return Course::GetAllStatuses()->firstWhere('name', CourseStatusEnum::PRESENTING->value);
    }

    public function ActiveAnswerSheetStatus()
    {
        return AnswerSheet::GetAllStatuses()->firstWhere('name', AnswerSheetStatusEnum::APPROVED->value);
    }

    public function isCourseCompleted($student)
    {
        $isComplete = Course::joinRelationship('lessons.lessonStudyLog', function ($query) {
            $query->where('is_completed', 1);
        })
            ->where('student_id', $student->id)
            ->exists();

        return $isComplete;
    }


    public function hasAttemptedAndPassedExam($student, $courseId)
    {
        $attempted = AnswerSheet::joinRelationship('exam.courseExams.course')
            ->where('courses.id', $courseId)
            ->where('answer_sheets.student_id', $student->id)
            ->exists();

        $status = $this->ActiveAnswerSheetStatus();

        $passed = AnswerSheet::joinRelationship('status', function ($query) use ($status) {
            $query->where('status_id', $status->id);
        })
            ->exists();

        if ($attempted && $passed) {
            return true;
        }

        return false;
    }


    public function courseCanceledStatus()
    {
        return Course::GetAllStatuses()->firstWhere('name', CourseStatusEnum::CANCELED->value);
    }

    public function courseEndedStatus()
    {
        return Course::GetAllStatuses()->firstWhere('name', CourseStatusEnum::ENDED->value);
    }

    public function coursePishnevisStatus()
    {
        return Course::GetAllStatuses()->firstWhere('name', CourseStatusEnum::PISHNEVIS->value);
    }


}
