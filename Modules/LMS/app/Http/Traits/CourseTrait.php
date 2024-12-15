<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Http\Enums\CourseStatusEnum;
use Modules\LMS\app\Http\Enums\LessonStatusEnum;
use Modules\LMS\app\Models\Course;

trait CourseTrait
{
    use AnswerSheetTrait;

    private static string $presenting = CourseStatusEnum::PRESENTING->value;
    private static string $ended = CourseStatusEnum::ENDED->value;
    private static string $canceled = CourseStatusEnum::CANCELED->value;
    private static string $deleted = CourseStatusEnum::DELETED->value;
    private static string $pishnevis = CourseStatusEnum::PISHNEVIS->value;
    private static string $bargozarShavande = CourseStatusEnum::ORGANIZER->value;


    public function courseIndex(int $perPage = 10, int $pageNumber = 1, array $data = [])
    {
        $searchTerm = $data['title'] ?? null;

        $query = Course::query()->withCount(['chapters', 'lessons', 'questions'])
            ->with('latestStatus');
        $query->whereHas('latestStatus', function ($query) {
            $query->whereIn('name', [CourseStatusEnum::PRESENTING->value, CourseStatusEnum::PISHNEVIS, CourseStatusEnum::WAITING_TO_PRESENT->value]);
        });

        $query->when($searchTerm, function ($query, $searchTerm) {
            $query->where('courses.title', 'like', '%' . $searchTerm . '%')
                ->whereRaw("MATCH (title) AGAINST (? IN BOOLEAN MODE)", [$searchTerm]);
        });
        return $query->paginate($perPage, ['*'], 'page', $pageNumber);
    }


    public function courseShow($course, $user)
    {
        //Take User Initial Info
        $user->load([
            'isEnrolled',
            'answerSheets',
            'student'
        ]);

        $enrolls = $user->isEnrolled;
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

        if ($enrolls->isEmpty()) {
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
                'prerequisiteCourses',
                'chapters.lessons' => function ($query) {
                    $query->whereHas('latestStatus', function ($query) {
                        $query->where('name', LessonStatusEnum::ACTIVE->value);
                    });
                },
            ],
            'StudyLog' => ['lessonStudyLog' => function ($query) use ($user) {
                $query->where('student_id', $user->student->id)
                    ->where('is_completed', true);
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
                $chapters = $component['data']['chapters.lessons'] ?? [];
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
                    if ($log['is_completed'] === 1) {
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
}

