<?php

namespace Modules\LMS\app\Http\Traits;

use Illuminate\Support\Facades\DB;
use Modules\HRMS\app\Models\Job;
use Modules\HRMS\app\Models\Level;
use Modules\HRMS\app\Models\Position;
use Modules\LMS\app\Http\Enums\AnswerSheetStatusEnum;
use Modules\LMS\app\Http\Enums\CourseStatusEnum;
use Modules\LMS\app\Http\Enums\LessonStatusEnum;
use Modules\LMS\app\Http\Enums\QuestionsEnum;
use Modules\LMS\app\Models\AnswerSheet;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\Enroll;
use Modules\LMS\app\Models\Lesson;
use Modules\LMS\app\Models\StatusCourse;
use Modules\OUnitMS\app\Models\VillageOfc;
use Modules\PayStream\app\Http\Traits\OrderTrait;
use Modules\PayStream\app\Models\FinancialStatus;
use Modules\PayStream\app\Models\ProcessStatus;
use Modules\StatusMS\app\Models\Status;


trait CourseTrait
{
    use AnswerSheetTrait, LessonTrait, OrderTrait, QuestionsTrait;

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

        $questionStatus = $this->questionActiveStatus();
        $query = Course::query()
            ->joinRelationship('cover')
            ->withCount('allActiveLessons')
            ->withCount('chapters')
            ->withCount(['questions' => function ($query) use ($questionStatus) {
                $query->where('status_id', $questionStatus->id);
            }])
            ->joinRelationship('statuses', ['statuses' => function ($join) {
                $join
                    ->on('status_course.id', '=', \DB::raw('(
                    SELECT id
                    FROM status_course AS cs
                    WHERE cs.course_id = courses.id
                    ORDER BY cs.create_date DESC
                    LIMIT 1
                )'))
                    ->whereIn('statuses.name', [$this::$presenting, $this::$waitToPresent, $this::$pishnevis]);
            }]);
        $query->addSelect([
            'courses.id as course_id',
            'courses.title',
            'courses.cover_id',
            'files.slug as cover_slug',
            'statuses.name as status_name',
            'statuses.class_name as status_class_name',
        ]);

        $query->when($searchTerm, function ($query) use ($searchTerm) {
            $query->whereRaw('MATCH(courses.title) AGAINST(?)', [$searchTerm])
                ->orWhere('courses.title', 'LIKE', '%' . $searchTerm . '%');
        });
        return $query->paginate($perPage);
    }


    public function lessonIndex(int $perPage = 10, int $pageNumber = 1, array $data = [])
    {
        $searchTerm = $data['name'] ?? null;
        $statusId = $this->lessonActiveStatus()->id;
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
            ->with(['status'])
            ->withCount([
                'chapters',
                'questions',
                'lessons' => function ($query) use ($statusId) {
                    $query->whereIn('lessons.id', function ($subQuery) use ($statusId) {
                        $subQuery->select('lesson_id')
                            ->from('status_lesson as sl1')
                            ->whereRaw('sl1.id = (SELECT MAX(sl2.id) FROM status_lesson as sl2 WHERE sl2.lesson_id = sl1.lesson_id)')
                            ->where('sl1.status_id', $statusId);
                    });
                }
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
        $course->access_date = isset($data['accessDate']) ? convertPersianToGregorianBothHaveTimeAndDont($data['accessDate']) : null;
        $course->expiration_date = isset($data['expireDate']) ? convertPersianToGregorianBothHaveTimeAndDont($data['expireDate']) : null;
        $course->preview_video_id = $data['previewVideoID'] ?? $course->preview_video_id;
        $course->cover_id = $data['coverID'] ?? $course->cover_id;
        $course->save();
        return $course;
    }

    public function courseShow($course, $user)
    {

        $user->load([
            'answerSheets',
            'student',
            'person.avatar'
        ]);

        $ans = Course::with('exams.answerSheets')->find($course->id);
        $examIds = $ans->exams->pluck('id')->toArray();
        $ansSheets = AnswerSheet::whereIn('exam_id', $examIds)
            ->where('student_id', $user->student->id)
            ->orderByDesc('id')
            ->limit(1)
            ->get();
        $isEnrolled = $this->isEnrolledToDefinedCourse($course->id, $user);

        $answerSheet = is_null($ansSheets->first()) ? null : $ansSheets->first();
        $student = $user->student;

        $AllowToDos = [
            "canRead" => false,
            "canTrainingExam" => false,
            "canReportCard" => false,
            "canFinalExam" => false,
            "canDegree" => false,
            "joined" => false
        ];


        $exampApprovedStatus = $this->answerSheetApprovedStatus()->id;

        $isApproveFromExam = ($answerSheet && $answerSheet->status_id == $exampApprovedStatus);


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
            'StudyLog' => [
                'lessonStudyLog' => function ($query) use ($user, $isEnrolled) {
                    $query->where('student_id', $user->student->id)
                        ->where('is_completed', true)
//                        ->where('study_count', '<=', ($isEnrolled?->isEnrolled[0]->orderable->study_count ?? 0) + 1)
                        ->whereHas('lesson.latestStatus', function ($query) {
                            $query->where('name', LessonStatusEnum::ACTIVE->value);
                        });
                }
            ]
        ]);

        $flattenedComponents = $componentsToRender->only($myPermissions->intersect($componentsToRender->keys())->toArray())
            ->flatMap(fn($relations) => collect($relations)->mapWithKeys(fn($relation, $key) => is_callable($relation) ? [$key => $relation] : [$relation => fn($query) => $query]))->all();


        $course = $course->load($flattenedComponents);

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

        $AllowToDos['canTrainingExam'] = false;
        $AllowToDos['canReportCard'] = false;

        if ($AllowToDos['joined']) {
            $studyCount = $AdditionalData["enrolled"]->isEnrolled[0]["orderable"]["study_count"];
            if ($studyCount > 0) {
                $AllowToDos["canFinalExam"] = true;
            }
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
                            if ($lesson->latestStatus()->first()->name === LessonStatusEnum::ACTIVE->value) {
                                $allLessons[] = $lesson['id'];
                            }
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
        if ($completionPercentage > 100) {
            $completionPercentage = 100;
        }
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
                        $firstLesson = $lesson->first();
                        $status = $this->checkLessonStatus($firstLesson->lesson_id);

                        return [
                            'id' => $firstLesson->lesson_id,
                            'title' => $firstLesson->lesson_title,
                            'isComplete' => $firstLesson->is_completed,
                            'duration' => convertSecondToMinute($firstLesson->files_duration),
                            'chapter_id' => $firstLesson->chapter_id,
                            'status' => $status->first()->name,
                        ];
                    })->values(),
                ];
            }
            return null;
        })->filter()->values();

        $activeLessons = collect($groupedData)
            ->flatMap(fn($chapter) => $chapter['lessons'])
            ->filter(fn($lesson) => $lesson['status'] == LessonStatusEnum::ACTIVE->value && $lesson['isComplete'] === null)
            ->pluck('id');

        if ($activeLessons->isNotEmpty()) {
            $lastLessonId = $activeLessons->first();
        } else {
            $lessonsWithIncomplete = collect($groupedData)
                ->flatMap(fn($chapter) => $chapter['lessons'])
                ->filter(fn($lesson) => $lesson['isComplete'] === null)
                ->pluck('id');

            if ($lessonsWithIncomplete->isNotEmpty()) {
                $lastLessonId = $lessonsWithIncomplete->first();
            } else {
                $lastLessonId = collect($groupedData)
                    ->flatMap(fn($chapter) => $chapter['lessons'])
                    ->pluck('id')
                    ->first();
            }
        }

        return [
            "lessonID" => $lastLessonId,
            "sidebar" => $data,
        ];


    }

    public function checkLessonStatus($id)
    {
        $lesson = Lesson::with('latestStatus')->find($id);
        return $lesson->latestStatus;
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
                'ounitFeatures' => fn($join) => $join->as('ounit_feature_alias')
                    ->on('ounit_feature_alias.course_target_id', '=', 'course_target_alias.id'),
                'value' => fn($join) => $join->as('value_alias')
                    ->on('value_alias.id', '=', 'ounit_feature_alias.ouc_property_value'),
                'oucProperty' => fn($join) => $join->as('oucProperty'),
            ])
            ->leftJoinRelationship('courseTarget.targetOunitCat', [
                'targetOunitCat' => fn($join) => $join->as('targetOunitCat'),
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
                'course_video_alias.size as course_video_size',
                'course_cover_alias.id as course_cover_id',
                'course_cover_alias.slug as course_cover_slug',
                'course_cover_alias.name as course_cover_title',
                'course_cover_alias.size as course_cover_size',
                'course_video_alias.id as course_video_id',
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
                'targetOunitCat.ounit_cat_id as ounit_category_id'
            ])
            ->where('courses.id', $id)
            ->get();

        $filteredResults = $query->groupBy('course_target_id')->map(function ($group) {
            if ($group->pluck('value_alias_value')->unique()->count() > 1 ||
                $group->pluck('oucProperty_name')->unique()->count() > 1) {
                return $group->filter(function ($item) {
                    return !(is_null($item->value_alias_value) && is_null($item->oucProperty_name));
                });
            }
            return $group;
        })->flatten(1);

        return $filteredResults;

    }

    public function showCourseDataForEnteshareDore($id)
    {
        $query = Course::query()
            ->leftJoinRelationshipUsingAlias('video', 'course_video_alias')
            ->leftJoinRelationshipUsingAlias('cover', 'course_cover_alias')
            ->leftJoinRelationshipUsingAlias('privacy', 'privacy_alias')
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
                'ounitFeatures' => fn($join) => $join->as('ounit_feature_alias')
                    ->on('ounit_feature_alias.course_target_id', '=', 'course_target_alias.id'),
                'value' => fn($join) => $join->as('value_alias')
                    ->on('value_alias.id', '=', 'ounit_feature_alias.ouc_property_value'),
                'oucProperty' => fn($join) => $join->as('oucProperty'),
            ])
            ->leftJoinRelationship('courseTarget.targetOunitCat', [
                'targetOunitCat' => fn($join) => $join->as('targetOunitCat'),
            ])
            ->leftJoinRelationship('chapters.lessons', [
                'lessons' => fn($join) => $join->as('lessons_alias'),
                'chapters' => fn($join) => $join->as('chapters_alias'),
            ])
            ->leftJoinRelationship('lastStatusForJoin.status', [
                "status" => fn($join) => $join->as('status_alias'),
                "lastStatusForJoin" => fn($join) => $join->as('course_status_alias')
            ])
            ->select([
                //course datas
                'courses.id as course_alias_id',
                'courses.title as course_alias_title',
                'courses.price as course_alias_price',
                'courses.description as course_alias_description',
                'courses.is_required as course_alias_is_required',
                'courses.expiration_date as course_alias_expiration_date',
                'courses.access_date as course_alias_access_date',
                'course_cover_alias.id as course_video_id',
                'course_cover_alias.slug as course_cover_slug',
                'course_cover_alias.name as course_cover_title',
                'course_cover_alias.size as course_cover_size',
                'course_video_alias.slug as course_video_slug',
                'course_video_alias.name as course_video_title',
                'course_video_alias.size as course_video_size',
                //Privacy
                'privacy_alias.id as privacy_alias_id',
                'privacy_alias.name as privacy_alias_name',
                //pre req data
                'pre_reg_alias.id as pre_reg_alias_id',
                'pre_reg_alias.title as pre_reg_alias_title',
                //targets
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
                'targetOunitCat.ounit_cat_id as ounit_category_id',
                //status
                'status_alias.name as status_alias_name',
                'status_alias.class_name as status_alias_class_name',
                'course_status_alias.id as course_status_alias_id',
                //chapters and lessons
                'chapters_alias.title as chapters_alias_title',
                'chapters_alias.id as chapters_alias_id',
                'lessons_alias.title as lessons_alias_title',
                'lessons_alias.id as lessons_alias_id',
            ])
            ->where('courses.id', $id)
            ->get();

        $filteredResults = $query->groupBy('course_target_id')->map(function ($group) {
            // Check if there are any items in the group with different value_alias_value or oucProperty_name
            if ($group->pluck('value_alias_value')->unique()->count() > 1 ||
                $group->pluck('oucProperty_name')->unique()->count() > 1) {
                // Keep only the item where both are not null, remove the one that is null
                return $group->filter(function ($item) {
                    return !(is_null($item->value_alias_value) && is_null($item->oucProperty_name));
                });
            }
            // Otherwise, return the group as is
            return $group;
        })->flatten(1); // Flatten to get the final list of results

        return $filteredResults;
// Flatten to get the final list of results

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
//        $attempted = AnswerSheet::joinRelationship('exam.courseExams.course')
//            ->where('courses.id', $courseId)
//            ->where('answer_sheets.student_id', $student->id)
//            ->exists();

        $status = $this->ActiveAnswerSheetStatus();

        $passed = AnswerSheet::joinRelationship('status', function ($query) use ($status) {
            $query->where('status_id', $status->id);
        })
            ->exists();

        if ($passed) {
            return true;
        }

        return false;
    }

    public function enrolledCourses($user)
    {
        $regStatus = $this::$proc_registered;
        $payedStatus = $this::$fin_pardakhtShode;
        $course = Course::query()
            ->joinRelationshipUsingAlias('cover', 'avatar_alias')
            ->leftJoinRelationship('chapters.lessons.lessonStudyLog', [
                'lessonStudyLog' => fn($query) => $query->as('lesson_study_log')
                    ->on('lesson_study_log.student_id', '=', DB::raw("'" . $user->customer->customerable_id . "'")),
                'lessons' => fn($query) => $query->as('lessons_alias')
            ])
            ->joinRelationship('chapters.lessons.contents.contentType', [
                'contentType' => fn($query) => $query->as('content_type_alias')
            ])
            ->joinRelationship('enrolls.order',
                ['order' => function ($query) use ($user, $payedStatus, $regStatus) {
                    $query
                        ->as('order_alias')
                        ->on('order_alias.orderable_type', '=', DB::raw("'" . addslashes(Enroll::class) . "'"))
                        ->on('order_alias.customer_id', '=', DB::raw("'" . $user->customer->id . "'"))
                        ->join('process_status as proc_status_alias', 'proc_status_alias.order_id', '=', 'order_alias.id')
                        ->join('financial_status as fin_status_alias', 'fin_status_alias.order_id', '=', 'order_alias.id')
                        ->join('statuses as status_fin_alias', function ($join) use ($payedStatus) {
                            $join->on('status_fin_alias.model', '=', DB::raw("'" . addslashes(FinancialStatus::class) . "'"))
                                ->where('status_fin_alias.name', '=', DB::raw("'" . addslashes($payedStatus) . "'"));
                        })
                        ->join('statuses as status_proc_alias', function ($join) use ($regStatus) {
                            $join->on('status_proc_alias.model', '=', DB::raw("'" . addslashes(ProcessStatus::class) . "'"))
                                ->where('status_proc_alias.name', '=', DB::raw("'" . addslashes($regStatus) . "'"));
                        });
                }]
            )
            ->select([
                'courses.id as course_alias_id',
                'avatar_alias.slug as avatar_alias_slug',
                'courses.title as course_alias_title',
                'content_type_alias.name as content_type_alias_name',
                'lesson_study_log.is_completed as lesson_is_completed',
                'lesson_study_log.is_completed as lesson_is_completed',
                'lesson_study_log.study_count as study_count_alias',
                'lessons_alias.id as lesson_id',
            ])
            ->whereHas('latestStatus', function ($query) {
                $query->whereIn('name', [$this::$presenting, $this::$ended, $this::$canceled]);
            })
            ->get();
        return $course;
    }

    public function getRelatedLists($title, $ounit, $level, $position, $job, $isTourism, $isFarm, $isAttachedToCity, $degree, $perPage, $pageNum)
    {
        $ids = array_column($ounit, 'id');
        $ounitCats = array_unique(array_column($ounit, 'category_id'));
        $courses = Course::query()
            ->join('status_course as status_course_alias', 'status_course_alias.course_id', '=', 'courses.id')
            ->join('statuses as statuses_alias', function ($join) {
                $join->on('statuses_alias.id', '=', 'status_course_alias.status_id')
                    ->whereRaw('status_course_alias.create_date = (SELECT MAX(create_date) FROM status_course WHERE course_id = courses.id)')
                    ->where('statuses_alias.name', '=', $this::$presenting);
            })
            ->leftJoin('files as cover_alias', 'cover_alias.id', '=', 'courses.cover_id')
            ->join('course_targets as targets_alias', function ($join) use ($ids) {
                $join->on('targets_alias.course_id', '=', 'courses.id');
                $join->whereIn('targets_alias.parent_ounit_id', $ids);
            })
            ->join('target_ounit_cat as target_ounit_cat_alias', 'target_ounit_cat_alias.course_target_id', '=', 'targets_alias.id')
            ->whereIn('target_ounit_cat_alias.ounit_cat_id', $ounitCats)
            ->leftJoin('course_employees_features as employee_feat_alias', 'employee_feat_alias.course_target_id', '=', 'targets_alias.id')
            ->where(function ($query) use ($level, $position, $job) {
                $query->whereIntegerInRaw('employee_feat_alias.propertyble_id', $level)
                    ->where('employee_feat_alias.propertyble_type', Level::class);

                if (!empty($position)) {
                    $query->orWhere(function ($subQuery) use ($position, $level) {
                        $subQuery->whereIntegerInRaw('employee_feat_alias.propertyble_id', $position)
                            ->where('employee_feat_alias.propertyble_type', Position::class);
                        $subQuery->whereIntegerInRaw('employee_feat_alias.propertyble_id', $level)
                            ->where('employee_feat_alias.propertyble_type', Level::class);
                    });
                }

                if (!empty($job)) {
                    $query->orWhere(function ($subQuery) use ($job) {
                        $subQuery->whereIntegerInRaw('employee_feat_alias.propertyble_id', $job)
                            ->where('employee_feat_alias.propertyble_type', Job::class);
                    });
                }

                // Include rows with NULL `employee_feat_alias`
                $query->orWhereNull('employee_feat_alias.id');
            })
            ->leftJoin('course_ounit_features as course_ounit_feat_alias', function ($join) {
                $join->on('course_ounit_feat_alias.course_target_id', '=', 'targets_alias.id');
            })
            ->leftJoin('ouc_property_values as ouc_prop_value', function ($join) {
                $join->on('ouc_prop_value.id', '=', 'course_ounit_feat_alias.ouc_property_value');
            })
            ->leftJoin('ouc_properties as ouc_prop_alias', function ($join) {
                $join->on('ouc_prop_alias.id', '=', 'ouc_prop_value.ouc_property_id')
                    ->on('ouc_prop_alias.ounit_cat_id', '=', 'target_ounit_cat_alias.ounit_cat_id');
            })
            ->leftJoin('organization_units as organ_alias', function ($join) use ($ids) {
                $join->whereIn('organ_alias.unitable_id', $ids)
                    ->where('organ_alias.unitable_type', VillageOfc::class);
            })
            ->leftJoin('village_ofcs as village_ofc_alias', function ($join) {
                $join->on('village_ofc_alias.id', '=', 'organ_alias.unitable_id');
            })
            ->where(function ($query) use ($isTourism, $isFarm, $isAttachedToCity, $degree) {
                if (!empty($isTourism)) {
                    $query->Where(function ($subQuery) use ($isTourism) {
                        $subQuery->whereIntegerInRaw('ouc_prop_value.value', $isTourism)
                            ->where('ouc_prop_alias.column_name', DB::raw("degree"));
                    });
                }
                if (!empty($isFarm)) {
                    $query->orWhere(function ($subQuery) use ($isFarm) {
                        $subQuery->whereIntegerInRaw('ouc_prop_value.value', $isFarm)
                            ->where('ouc_prop_alias.column_name', DB::raw("isFarm"));
                    });
                }

                if (!empty($isAttachedToCity)) {
                    $query->orWhere(function ($subQuery) use ($isAttachedToCity) {
                        $subQuery->whereIntegerInRaw('ouc_prop_value.value', $isAttachedToCity)
                            ->where('ouc_prop_alias.column_name', DB::raw("isAttached_to_city"));
                    });
                }

                if (!empty($degree)) {
                    $query->orWhere(function ($subQuery) use ($degree) {
                        $subQuery->whereIntegerInRaw('ouc_prop_value.value', $degree)
                            ->where('ouc_prop_alias.column_name', DB::raw("degree"));
                    });
                }

                $query->orWhereNull('ouc_prop_alias.id');
            })
            ->select([
                'courses.id as id',
                'courses.title as course_title',
                'courses.expiration_date as course_exp_date',
                'statuses_alias.name as status_name',
                'statuses_alias.class_name as class_name',
                'cover_alias.slug as cover_slug',
            ])
            ->withCount('allActiveLessons')
            ->with(['contentTypes' => function ($query) {
                $query->distinct();
            }])
            ->where('courses.title', 'like', '%' . $title . '%')
            ->distinct()
            ->paginate($perPage, ['*'], $pageNum);

        return $courses;
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

    public function courseDeletedStatus()
    {
        return Course::GetAllStatuses()->firstWhere('name', CourseStatusEnum::DELETED->value);
    }

    public function coursePresentingStatus()
    {
        return Course::GetAllStatuses()->firstWhere('name', CourseStatusEnum::PRESENTING->value);
    }

    public function courseWaitPresentingStatus()
    {
        return Course::GetAllStatuses()->firstWhere('name', CourseStatusEnum::WAITING_TO_PRESENT->value);
    }

}
