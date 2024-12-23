<?php

namespace Modules\LMS\app\Http\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Modules\AAA\app\Models\User;
use Modules\LMS\app\Http\Enums\CourseStatusEnum;
use Modules\LMS\app\Http\Enums\LessonStatusEnum;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\Lesson;
use Modules\LMS\app\Models\Teacher;

trait CourseTrait
{
    use AnswerSheetTrait;

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

        $query = Course::query()->joinRelationship('cover');
        $query->select([
            'courses.id',
            'courses.title',
            'courses.cover_id',
            'files.slug as cover_slug',
        ]);
        $query
            ->when($searchTerm, function ($query) use ($searchTerm) {
                $query->whereRaw('MATCH(courses.title) AGAINST(?)', [$searchTerm])
                    ->orWhere('courses.title', 'LIKE', '%' . $searchTerm . '%');
            });
        $query->withCount(['chapters', 'lessons', 'questions']);

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


    public function courseShow($course, $user)
    {
        //Take User Initial Info
        $user->load([
            'answerSheets',
            'student',
            'person.avatar'
        ]);

        $isEnrolled = $this -> isEnrolledToDefinedCourse($course->id , $user);

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
                'prerequisiteCourses'=> function ($query) {
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
            'StudyLog' => ['lessonStudyLog' => function ($query) use ($user , $isEnrolled) {
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


    public function coursePresentingStatus()
    {
        return Course::GetAllStatuses()->firstWhere('name', CourseStatusEnum::PRESENTING->value);
    }

    public function courseCanceledStatus()
    {
        return Course::GetAllStatuses()->firstWhere('name', CourseStatusEnum::CANCELED->value);
    }

    public function courseEndedStatus()
    {
        return Course::GetAllStatuses()->firstWhere('name', CourseStatusEnum::ENDED->value);
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



    public function isJoinedPreRerequisites($user , $course)
    {
        $preCoursesIds = $course->prerequisiteCourses->pluck('id')->toArray();

        $enrolls = [];
        foreach($preCoursesIds as $preCourseId)
        {
            $user -> load(['enrolls' => function ($q) use ($preCourseId){
                $q->where('course_id' , $preCourseId);
            }]);
            if(!empty($user -> enrolls[0])){                            $enrolls[] = true;
            }else{
                $enrolls[] = false;
            }
        }

        if (in_array(false, $enrolls, true)) {
            return false;
        } else {
            return true;
        }

    }


    public function dataShowViewCourse($course , $user)
    {
        $course = Course::joinRelationship('chapters', function ($join) {
            $join->as('chapter_alias');
        })
            ->join('lessons as lesson_alias', 'chapter_alias.id', '=', 'lesson_alias.chapter_id')
            ->join('contents as content_alias', 'lesson_alias.id', '=', 'content_alias.lesson_id')
            ->join('files as files_alias', 'content_alias.file_id', '=', 'files_alias.id')
            ->join('content_type as ct_alias', 'content_alias.content_type_id', '=', 'ct_alias.id')
            ->join('work_forces as wf_alias', function ($join) {
                $join->on('wf_alias.workforceable_id', '=', 'content_alias.teacher_id')
                    ->where('wf_alias.workforceable_type', '=', Teacher::class);
            })
            ->join('persons as person_alias', 'person_alias.id', '=', 'wf_alias.person_id')
            ->leftJoin('comments as comments_alias', function ($join) use ($user){
                $join->on('comments_alias.commentable_id' , '=' , 'lesson_alias.id')
                    ->where('comments_alias.creator_id', '=', $user -> id)
                    ->where('comments_alias.commentable_type' , '=' , Lesson::class);
            })
            ->leftJoin('users as commented_user_alias' , 'comments_alias.creator_id' , '=' , 'commented_user_alias.id')
            ->leftJoin('persons as commented_person_alias' , 'commented_user_alias.person_id' , '=' , 'commented_person_alias.id')

            ->select([
                'chapter_alias.id as chapter_id',
                'chapter_alias.title as chapter_title',
                'chapter_alias.description as chapter_description',
                'lesson_alias.id as lesson_id',
                'lesson_alias.title as lesson_title',
                'lesson_alias.description as lesson_description',
                'content_alias.id as content_id',
                'content_alias.name as content_title',
                'files_alias.slug as files_slug',
                'ct_alias.name as content_type_name',
                'person_alias.display_name as teacher_name',
                'comments_alias.text as comment_text',
                'comments_alias.create_date as comment_created_at',
                'commented_person_alias.display_name as commented_person_name',
            ])
            ->where('courses.id', $course -> id)
            ->get();
        return $course;
    }

}

