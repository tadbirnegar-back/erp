<?php

namespace Modules\LMS\app\Http\Traits;

use DB;
use Modules\AAA\app\Models\User;
use Modules\CustomerMS\app\Http\Traits\CustomerTrait;
use Modules\EMS\app\Http\Traits\DateTrait;
use Modules\HRMS\app\Http\Enums\OunitCategoryEnum;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\LMS\app\Http\Enums\ContentTypeEnum;
use Modules\LMS\app\Http\Enums\LessonStatusEnum;
use Modules\LMS\app\Http\Enums\RepositoryEnum;
use Modules\LMS\app\Models\AnswerSheet;
use Modules\LMS\app\Models\ContentType;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\CourseEmployeeFeature;
use Modules\LMS\app\Models\CourseOunitFeature;
use Modules\LMS\app\Models\CourseTarget;
use Modules\LMS\app\Models\OucProperty;
use Modules\LMS\app\Models\OucPropertyValue;
use Modules\LMS\app\Models\Question;
use Modules\LMS\app\Models\Repository;
use Modules\LMS\app\Models\Student;
use Modules\LMS\app\Models\TargetOunitCat;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Morilog\Jalali\Jalalian;
trait ReportingTrait
{
    use AnswerSheetTrait, LessonTrait, ContentTrait, DateTrait, CustomerTrait;


    public function ans($answerSheetID, $user, $data, $courseID)
    {
        $repo = Repository::where('name', RepositoryEnum::FINAL->value)->first()->id;
        $studentID = $user->student->id;
        $mostRecentAnswerSheet = Question::joinRelationship('answers.answerSheet.status')
            ->joinRelationship('answers.answerSheet.exam.courseExams.course')
            ->joinRelationship('repository')
            ->select([
                'answer_sheets.start_date_time',
                'answer_sheets.student_id as studentID',
                'statuses.name as statusName',
                'answer_sheets.score as score',
                'answer_sheets.exam_id as examID',
                'answer_sheets.id as answerSheetID',
            ])
            ->where('repositories.id', $repo)
            ->where('courses.id', $courseID)
            ->where('answer_sheets.student_id', $studentID)
            ->orderBy('answer_sheets.start_date_time', 'desc')
            ->first();
        $studentInfo = $this->studentInfo($user);

        if (!$mostRecentAnswerSheet) {
            return [
                'message' => 'هیچ آزمونی برای این دانشجو یافت نشد.',
                'answerSheetOfFinalExam' => null,
                'studentInfo' => $studentInfo,
                'finalExamEnrollment' => 0,
                'practiceExam' => $this->practicalExam($answerSheetID, $user, $data, $courseID),
                'FailedExams' => [],
                'courseInformation' => $this->courseInfo($studentID, $courseID, $user),
            ];
        }

        $examId = $mostRecentAnswerSheet->examID;
        $examFinalCount = $this->examFinalCount($studentID, $courseID, $repo);
        $practicalExam = $this->practicalExam($answerSheetID, $user, $data, $courseID);
        $optionID = array_filter(array_column($data['questions'], 'option_id'));
        $calculate = $this->counting($optionID, $answerSheetID, $examId, $repo);
        $failedExams = $this->FailedExams($studentID, $courseID, $repo);
        $courseInfo = $this->courseInfo($studentID, $courseID, $user);


        return [
            'calculate' => $calculate,
            'answerSheetOfFinalExam' => $mostRecentAnswerSheet,
            'studentInfo' => $studentInfo,
            'finalExamEnrollment' => $examFinalCount,
            'practiceExam' => $practicalExam,
            'FailedExams' => $failedExams,
            'courseInformation' => $courseInfo,
        ];
    }

    public function studentInfo($student)
    {
        $query = User::with(['person.avatar', 'activeRecruitmentScripts.position'])
            ->where('users.id', $student->id)
            ->first();

        return [
            'name' => $query->person->display_name ?? null,
            'avatar' => $query->person->avatar->slug ?? null,
            'poseName' => $query->activeRecruitmentScripts->first()->position->name ?? null,
        ];
    }


    public function counting($optionID, $answerSheetID, $examId, $repo)
    {
        $correctAnswers = $this->correct($optionID, $repo);
        $falseAnswers = $this->false($optionID, $repo);
        $nullAnswers = $this->nullAnswers($answerSheetID);
        $questionCount = $this->questions($examId, $repo);

        return [
            'correct' => $correctAnswers,
            'false' => $falseAnswers,
            'null' => $nullAnswers,
            'allQuestions' => $questionCount,
        ];
    }

    public function correct($optionIDs, $repo)
    {
        $count = 0;

        foreach ($optionIDs as $optionID) {
            $count += Question::join('repositories', 'questions.repository_id', '=', 'repositories.id')
                ->join('options', 'questions.id', '=', 'options.question_id')
                ->where('options.id', $optionID)
                ->where('options.is_correct', 1)
                ->where('repositories.id', $repo)
                ->count();
        }

        return $count;
    }


    public function false($optionID, $repo)
    {
        return Question::joinRelationship('repository')
            ->joinRelationship('options')
            ->whereIn('options.id', $optionID)
            ->where('is_correct', 0)
            ->where('repositories.id', $repo)
            ->count();
    }


    public function questions($examId, $repo)
    {
        return Question::joinRelationship('repository')
            ->joinRelationship('questionExams')
            ->where('exam_id', $examId)
            ->where('repositories.id', $repo)
            ->count('question_id');
    }


    public function examFinalCount($studentID, $courseID, $repo)
    {
        $countExams = AnswerSheet::joinRelationship('exam.courseExams.course')
            ->joinRelationship('exam.courseExams')
            ->joinRelationship('answers.questions.repository')
            ->select([
                'answer_sheets.student_id as studentID',
                'answer_sheets.exam_id as examID',
                'courses.id as courseID',
            ])
            ->where('answer_sheets.student_id', $studentID)
            ->where('courses.id', $courseID)
            ->where('repositories.id', $repo)
            ->distinct()
            ->get()
            ->count();
        return $countExams;
    }

    public function FailedExams($studentID, $courseID, $repo)
    {
        $failedExams = Question::joinRelationship('answers.answerSheet.exam.courseExams.course')
            ->joinRelationship('repository')
            ->select([
                'exams.title as examTitle',
                'exams.id as examID',
                'answer_sheets.start_date_time as startTime',
            ])
            ->where('repositories.id', $repo)
            ->where('courses.id', $courseID)
            ->where('answer_sheets.student_id', $studentID)
            ->where('answer_sheets.status_id', $this->answerSheetDeclinedStatus()->id)
            ->distinct()
            ->get();

        return $failedExams;

    }


    public function CourseInfo($studentID, $courseID, $user)
    {
        $contentTypes = ContentType::where('name', ContentTypeEnum::AUDIO)->first()->id;
        $VidContentTypes = ContentType::where('name', ContentTypeEnum::VIDEO)->first()->id;
        $course = Course::leftJoinRelationship('chapters.lessons.contents.file')
            ->leftJoinRelationship('courseExams.exams.answerSheets')
            ->leftJoinRelationship('chapters.lessons.contents.contentType')
            ->select([
                'courses.title as courseTitle',
            ])
            ->withCount('chapters')
            ->withCount('allActiveLessons')
            ->where('courses.id', $courseID)
            ->distinct()
            ->get()
            ->map(function ($item) {
                $item->chapters_count = $item->chapters_count == 0 ? null : $item->chapters_count;
                $item->all_active_lessons_count = $item->all_active_lessons_count == 0 ? null : $item->all_active_lessons_count;
                return $item;
            });


        $durationAudio = $this->AudioDuration($studentID, $courseID, $contentTypes);
        $durationVideo = $this->VideoDuration($studentID, $courseID, $VidContentTypes);
        $sumAudio = $durationAudio['total'];
        $sumVideo = $durationVideo['total'];
        $totalDuration = $sumAudio + $sumVideo;

        $completionPercentage = $this->completionPercentage($courseID, $studentID);

        $enrolled = $this->enrolled($courseID, $user);

        return [
            'course' => $course->first(),
            'durationOfAudio' => $durationAudio,
            'durationOfVideo' => $durationVideo,
            'totalDuration' => $totalDuration,
            'completionPercentage' => $completionPercentage,
            'erolled' => $enrolled,
        ];
    }

    public function enrolled($courseID, $user)
    {
        $user->load(['enrolls' => function ($q) use ($courseID) {
            $q->where('course_id', $courseID);
        }]);

        $studyCount = $user->enrolls->sum('study_count');

        return [
            'study_count' => $studyCount,
        ];
    }



    public function AudioDuration($studentID, $courseID, $contentTypes)
    {
        $lessonActiveStatus = $this->lessonActiveStatus()->id;
        $contentStatus = $this->contentActiveStatus()->id;

        $course = Course::joinRelationship('chapters.lessons.contents.contentType')
            ->joinRelationship('chapters.lessons.contents.file')
            ->joinRelationship('chapters.lessons.statuses', ['statuses' => function ($join) {
                $join->whereRaw('status_lesson.created_date = (SELECT MAX(created_date) FROM status_lesson WHERE lesson_id = lessons.id)')
                    ->where('statuses.name', '=', LessonStatusEnum::ACTIVE->value);
            }])
            ->select([
                'files.duration as duration',
            ])
            ->where('courses.id', $courseID)
            ->where('content_type.id', $contentTypes)
            ->where('contents.status_id', $contentStatus)
            ->get();
        $totalDuration = $course->sum(function ($item) {
            return $item->duration == 0 ? null : $item->duration;
        });
        $total =  $this -> calculateConsumeDataMyCourse($courseID, $studentID, $contentTypes , $contentStatus);
        return [
            'duration' => $totalDuration,
            'total' => $total,
        ];

    }

    private function calculateConsumeDataMyCourse($courseID, $studentID , $contentTypes , $contentActiveStatusId)
    {
        $consumeLog = Course::with(['allActiveLessons.contents' => function ($q) use ($courseID, $studentID, $contentTypes, $contentActiveStatusId) {
            $q->where('status_id', $contentActiveStatusId);
            $q->where('content_type_id', $contentTypes);
            $q->with(['consumeLog' => function ($query) use ($studentID) {
                $query->where('student_id', $studentID);
            }]);
            $q->with('file');
        }])->find($courseID);

        $total = 0;

        $contents = $consumeLog?->allActiveLessons?->flatMap->contents;

        if ($contents) {
            foreach ($contents as $item) {
                if ($item->consumeLog !== null && $item->file !== null) {
                    $completedOnes = $item->consumeLog->consume_round * $item->file->duration;
                    $total += $completedOnes + ($item->consumeLog->consume_data ?? 0);
                }
            }
        }

        return $total;

    }


    private function calculateAllConsumesDataMyCourse($courseID, $contentTypes, $contentActiveStatusId)
    {
        $consumeLog = Course::with(['allActiveLessons.contents' => function ($q) use ($courseID, $contentTypes, $contentActiveStatusId) {
            $q->where('status_id', $contentActiveStatusId);
            $q->where('content_type_id', $contentTypes);
            $q->with('consumeLogs', 'file');
        }])->find($courseID);

        $total = 0;

        $contents = $consumeLog?->allActiveLessons?->flatMap->contents;

        if ($contents) {
            foreach ($contents as $item) {
                if ($item->consumeLogs != []&& $item->file !== null) {
                    foreach ($item->consumeLogs as $log) {
                        $completedOnes = $log->consume_round * $item->file->duration;
                        $total += $completedOnes + ($log->consume_data ?? 0);
                    }
                }
            }
        }

        return $total;
    }


    public function videoDuration($studentID, $courseID, $contentTypes)
    {
        $lessonActiveStatus = $this->lessonActiveStatus()->id;
        $contentStatus = $this->contentActiveStatus()->id;

        $course = Course::joinRelationship('chapters.lessons.contents.contentType')
            ->joinRelationship('chapters.lessons.contents.file')
            ->joinRelationship('chapters.lessons.statuses', ['statuses' => function ($join) {
                $join->whereRaw('status_lesson.created_date = (SELECT MAX(created_date) FROM status_lesson WHERE lesson_id = lessons.id)')
                    ->where('statuses.name', '=', LessonStatusEnum::ACTIVE->value);
            }])
            ->select([
                'files.duration as duration',
            ])
            ->where('courses.id', $courseID)
            ->where('content_type.id', $contentTypes)
            ->where('contents.status_id', $contentStatus)
            ->get();
        $totalDuration = $course->sum(function ($item) {
            return $item->duration == 0 ? null : $item->duration;
        });
        $total =  $this -> calculateConsumeDataMyCourse($courseID, $studentID, $contentTypes , $contentStatus);
        return [
            'duration' => $totalDuration,
            'total' => $total,
        ];

    }

    public function practicalExam($answerSheetID, $user, $data, $courseID)
    {
        $practiceRepo = Repository::where('name', RepositoryEnum::PRACTICE->value)->first()->id;
        $studentID = $user->student->id;
        $AnswerSheet = Question::joinRelationship('answers.answerSheet.exam.courseExams.course')
            ->joinRelationship('status')
            ->joinRelationship('repository')
            ->select([
                'answer_sheets.start_date_time as startTime',
                'answer_sheets.score as score',
                'answer_sheets.exam_id as examID'
            ])
            ->where('repositories.id', $practiceRepo)
            ->where('courses.id', $courseID)
            ->where('answer_sheets.student_id', $studentID)
            ->distinct()
            ->get();
        $examId = $AnswerSheet->pluck('examID')->toArray();
        $score = $AnswerSheet->pluck('score')->toArray();
        $examCount = $this->examPracticalCount($studentID, $courseID, $practiceRepo);
        $optionID = array_filter(array_column($data['questions'], 'option_id'));
        $calculate = $this->count($optionID, $answerSheetID, $examId, $practiceRepo);
        return [
            'calculate' => $calculate,
            'answerSheetOfPracticalExam' => $AnswerSheet,
            'practicalExamEnrollment' => ($examCount == 0) ? null : $examCount,
            'scoreAverage' => count($score) > 0 ? array_sum($score) / count($score) : null,
        ];
    }

    public function examPracticalCount($studentID, $courseID, $practiceRepo)
    {
        $countExams = AnswerSheet::joinRelationship('exam.courseExams.course')
            ->joinRelationship('exam.courseExams')
            ->joinRelationship('answers.questions.repository')
            ->select([
                'answer_sheets.student_id as studentID',
                'answer_sheets.exam_id as examID',
                'courses.id as courseID',
            ])
            ->where('answer_sheets.student_id', $studentID)
            ->where('courses.id', $courseID)
            ->where('repositories.id', $practiceRepo)
            ->distinct()
            ->get()
            ->count();
        return $countExams;
    }

    public function count($optionID, $answerSheetID, $examId, $practiceRepo)
    {
        $correctAnswers = $this->correctQuestionAnswers($optionID, $practiceRepo);
        $falseAnswers = $this->inCorrectAnswers($optionID, $practiceRepo);
        $nullAnswers = $this->nullAnswers($answerSheetID);
        $questionCount = $this->questionsCount($examId, $practiceRepo);

        return [
            'correct' => ($correctAnswers == 0) ? null : $correctAnswers,
            'false' => ($falseAnswers == 0) ? null : $falseAnswers,
            'null' => ($nullAnswers == 0) ? null : $nullAnswers,
            'allQuestions' => ($questionCount == 0) ? null : $questionCount,
        ];
    }

    public function questionsCount($examId, $practiceRepo)
    {
        return Question::joinRelationship('repository')
            ->joinRelationship('questionExams')
            ->where('exam_id', $examId)
            ->where('repositories.id', $practiceRepo)
            ->count();


    }

    public function correctQuestionAnswers($optionIDs, $practiceRepo)
    {
        $count = 0;

        foreach ($optionIDs as $optionID) {
            $count += Question::join('repositories', 'questions.repository_id', '=', 'repositories.id')
                ->join('options', 'questions.id', '=', 'options.question_id')
                ->where('options.id', $optionID)
                ->where('options.is_correct', 1)
                ->where('repositories.id', $practiceRepo)
                ->count();
        }

        return $count;
    }

    public function inCorrectAnswers($optionID, $practiceRepo)
    {
        return Question::joinRelationship('repository')
            ->joinRelationship('options')
            ->whereIn('options.id', $optionID)
            ->where('is_correct', 0)
            ->where('repositories.id', $practiceRepo)
            ->count();
    }

    public function completionPercentage($courseID, $studentID)
    {
        $completion = Course::joinRelationship('chapters.lessons.lessonStudyLog')
            ->joinRelationship('chapters.lessons.statuses')
            ->select([
                'lesson_study_log.is_completed as is_completed',
                'lesson_study_log.id as lesson_study_log_id',
            ])
            ->where('courses.id', $courseID)
            ->where('lesson_study_log.student_id', $studentID)
            ->distinct()
            ->get();

        $totalLessons = Course::where('id', $courseID)->withCount('allActiveLessons')->first()->all_active_lessons_count ?? 0;
        $completedLessons = $completion->where('is_completed', 1)->count();
        return ($totalLessons > 0) ? ($completedLessons / $totalLessons) * 100 : 0;
    }

    public function CourseInformation($courseID)
    {

        $contentTypes = ContentType::where('name', ContentTypeEnum::AUDIO->value)->first()->id;
        $VidContentTypes = ContentType::where('name', ContentTypeEnum::VIDEO->value)->first()->id;
        $course = Course::leftJoinRelationship('chapters.lessons.contents.file')
            ->leftJoinRelationship('courseExams.exams.answerSheets')
            ->joinRelationship('chapters.lessons.contents.contentType')
            ->select([
                'courses.title as courseTitle',
            ])
            ->withCount('chapters')
            ->withCount('allActiveLessons')
            ->where('courses.id', $courseID)
            ->distinct()
            ->get();

        $durationAudio = $this->AudioCourseDuration($courseID, $contentTypes);
        $durationVideo = $this->VideoCourseDuration($courseID, $VidContentTypes);
        $sumAudio = $durationAudio['total'];
        $sumVideo = $durationVideo['total'];
        $totalDuration = $sumAudio + $sumVideo;

        $avgAudio = $durationAudio['averageOfAudio'];
        $avgVideo = $durationVideo['averageOfVideo'];
        $totalStudyDurationAverage = ($avgAudio + $avgVideo);
        $certificatesCount = $this->certificatesCount($courseID);
        $enrolledStudentsAndScoreAverage = $this->enrolledStudentsAndScoreAverage($courseID);
        $approvedStudents = $this->countAnswerSheetApprovedStatusOfStudents($courseID);
        $declinedStudents = $this->countAnswerSheetDeclinedStatusOfStudents($courseID);
        $allStudentsCount = $this->allStudentsCount($courseID);
        $subCount = $this->subCount($courseID);
        $months = $this->scoresAndMonthChartData($courseID);
        $cover = $this->CourseCover($courseID);
        $mashmuls = $this->CountOfMashmuls($courseID);


        return [
            'course' => $course->first(),
            'durationOfAudio' => $durationAudio,
            'durationOfVideo' => $durationVideo,
            'totalPlayedDuration' => $totalDuration,
            'certificatesCount' => $certificatesCount,
            'scoreAverageAndEnrolledStudents' => $enrolledStudentsAndScoreAverage,
            'approvedStudents' => $approvedStudents,
            'declinedStudents' => $declinedStudents,
            'allStudents' => $allStudentsCount,
            'subCount' => $subCount,
            'scoreAndMonthChart' => $months,
            'totalStudyDurationAverage' => $totalStudyDurationAverage,
            'cover' => $cover,
            'includedStudents' => $mashmuls
        ];
    }

    public function CourseCover($courseID)
    {
        $course = Course::with(['cover'])->find($courseID);

        if (!$course) {
            return response()->json([
                'error' => 'Course not found',
            ], 404);
        }

        if (!$course->cover) {
            return response()->json([
                'error' => 'Cover not found for this course',
            ], 404);
        }

        $relativePath = $course->cover->slug;

        $coverUrl = $relativePath ? url($relativePath) : asset('images/default-cover.png');

        return ([
            'avatar' => $coverUrl,
        ]);
    }

    public function AudioCourseDuration($courseID, $contentTypes)
    {
        $lessonActiveStatus = $this->lessonActiveStatus()->id;
        $contentStatus = $this->contentActiveStatus()->id;

        $course = Course::joinRelationship('chapters.lessons.contents.contentType')
            ->joinRelationship('chapters.lessons.contents.file')
            ->joinRelationship('chapters.lessons.statuses', ['statuses' => function ($join) {
                $join->whereRaw('status_lesson.created_date = (SELECT MAX(created_date) FROM status_lesson WHERE lesson_id = lessons.id)')
                    ->where('statuses.name', '=', LessonStatusEnum::ACTIVE->value);
            }])
            ->select([
                'files.duration as duration',
            ])
            ->withCount('enrolls')
            ->where('courses.id', $courseID)
            ->where('content_type.id', $contentTypes)
            ->where('contents.status_id', $contentStatus)
            ->get();

        $totalDuration = $course->sum(function ($item) {
            return $item->duration == 0 ? null : $item->duration;
        });
        $total =  $this -> calculateAllConsumesDataMyCourse($courseID, $contentTypes , $contentStatus);
        return [
            'duration' => $totalDuration,
            'total' => $total,
            'averageOfAudio' => $total / $course[0]->enrolls_count
        ];

    }

    public function VideoCourseDuration($courseID, $contentTypes)
    {
        $lessonActiveStatus = $this->lessonActiveStatus()->id;
        $contentStatus = $this->contentActiveStatus()->id;

        $course = Course::joinRelationship('chapters.lessons.contents.contentType')
            ->joinRelationship('chapters.lessons.contents.file')
            ->joinRelationship('chapters.lessons.statuses', ['statuses' => function ($join) {
                $join->whereRaw('status_lesson.created_date = (SELECT MAX(created_date) FROM status_lesson WHERE lesson_id = lessons.id)')
                    ->where('statuses.name', '=', LessonStatusEnum::ACTIVE->value);
            }])
            ->select([
                'files.duration as duration',
            ])
            ->withCount('enrolls')
            ->where('courses.id', $courseID)
            ->where('content_type.id', $contentTypes)
            ->where('contents.status_id', $contentStatus)
            ->get();

        $totalDuration = $course->sum(function ($item) {
            return $item->duration == 0 ? null : $item->duration;
        });
        $total =  $this -> calculateAllConsumesDataMyCourse($courseID, $contentTypes , $contentStatus);
        return [
            'duration' => $totalDuration,
            'total' => $total,
            'averageOfVideo' => $total / $course[0]->enrolls_count
        ];

    }

    public function certificatesCount($courseID)
    {
        return Course::joinRelationship('enrolls')
            ->whereNotNull('certificate_file_id')
            ->where('enrolls.course_id', $courseID)
            ->count();
    }

    public function enrolledStudentsAndScoreAverage($courseID)
    {
        $repo = Repository::where('name', RepositoryEnum::FINAL->value)->first()->id;

        $ans = Question::joinRelationship('answers.answerSheet.status')
            ->joinRelationship('answers.answerSheet.exam.courseExams.course')
            ->joinRelationship('repository')
            ->select('answer_sheets.score as scores')
            ->where('repositories.id', $repo)
            ->where('courses.id', $courseID)
            ->distinct();

        $averageScore = $ans->get()->pluck('scores')->avg();

        return [
            'average' => $averageScore,
            'EnrolledStudents' => $ans->count('answer_sheets.student_id'),
        ];
    }

    public function countAnswerSheetApprovedStatusOfStudents($courseID)
    {
        $repo = Repository::where('name', RepositoryEnum::FINAL->value)->first()->id;
        $passStatus = $this->answerSheetApprovedStatus()->id;
        $count = AnswerSheet::joinRelationship('answers.questions')
            ->joinRelationship('status')
            ->joinRelationship('exam.courseExams.course', [
                'course' => fn($join) => $join->as('course_alias'),
                'exam' => fn($join) => $join->as('exam_alias')
            ])
            ->joinRelationship('repository')
            ->where('repositories.id', $repo)
            ->where('answer_sheets.status_id', $passStatus)
            ->where('course_alias.id', $courseID)
            ->latest('answer_sheets.finish_date_time')
            ->distinct()
            ->get();
        return $count->count();
    }

    public function countAnswerSheetDeclinedStatusOfStudents($courseID)
    {
        $approved = $this->countAnswerSheetApprovedStatusOfStudents($courseID);
        $enrollsThatAreNotApproved = $this->enrollsThatAreNotApproved($courseID);

        return $enrollsThatAreNotApproved - $approved;
    }

    public function enrollsThatAreNotApproved($courseID)
    {
        $status = $this->activeCustomerStatus()->id;

        $query = Course::joinRelationship('enrolls.order.customer')
            ->where('courses.id', $courseID)
            ->where('customers.customerable_type', Student::class)
            ->where('customers.status_id', $status);
        return $query->count();

    }


    public function allStudentsCount($courseID)
    {
        return Course::joinRelationship('enrolls')
            ->where('enrolls.course_id', $courseID)
            ->count();
    }

    public function subCount($courseID)
    {
        $studyCompletedCount = Course::joinRelationship('enrolls')
            ->where('enrolls.course_id', $courseID)
            ->where('enrolls.study_completed', 1)
            ->count();

        $isStudyingCount = Course::joinRelationship('enrolls')
            ->where('enrolls.course_id', $courseID)
            ->where('enrolls.study_completed', 0)
            ->count();

        return [
            'studyCompleted' => $studyCompletedCount,
            'isStudying' => $isStudyingCount,
        ];
    }


    public function scoresAndMonthChartData($courseID)
    {
        $repo = Repository::where('name', RepositoryEnum::FINAL->value)->first()->id;

        $query = Course::joinRelationship('courseExams.exams.answerSheets.answers.questions.repository')
            ->select([
                'answer_sheets.score as scores',
                'answer_sheets.finish_date_time as finish_date_time',
            ])
            ->where('courses.id', $courseID)
            ->where('repositories.id', $repo)
            ->get();

        $persianMonths = [
            "فروردین", "اردیبهشت", "خرداد", "تیر", "مرداد", "شهریور",
            "مهر", "آبان", "آذر", "دی", "بهمن", "اسفند"
        ];

        $groupedData = $query->groupBy(function ($item) {
            $persianDate = Jalalian::fromDateTime($item->finish_date_time);
            $monthNumber = $persianDate->getMonth();
            return $this->humanReadableDate($monthNumber);
        });

        $result = [];
        foreach ($persianMonths as $monthName) {
            if ($groupedData->has($monthName)) {
                $items = $groupedData[$monthName];
                $scores = $items->pluck('scores');
                $averageScore = round($scores->avg(), 2);
            } else {
                $averageScore = 0;
            }

            $result[] = [
                'month' => $monthName,
                'average_score' => $averageScore,
            ];
        }

        return $result;
    }


    public function CountOfMashmuls($courseId)
    {
        $course = Course::find($courseId);
        $courseTargets = $this->GetCourseTarget($course->id);
        $courseOunitFeatures = $this->getOunitFeatures($courseTargets);
        $propValues = $this->getPropertyValueFeatures($courseOunitFeatures);
        $props = $this->getProperties($propValues);


        return $this->CountingAllTheCourseContainers($course, $courseTargets, $propValues, $props);


    }


    private function GetCourseTarget($courseId)
    {
        return CourseTarget::where('course_id', $courseId)->get();
    }

    private function getEmployeeFeatures($courseTargets)
    {
        $courseTargets = $courseTargets->pluck('id')->toArray();
        return CourseEmployeeFeature::whereIn('course_target_id', $courseTargets)->get();
    }


    private function getOunitFeatures($courseTargets)
    {
        $courseTargets = $courseTargets->pluck('id')->toArray();
        return CourseOunitFeature::whereIn('course_target_id', $courseTargets)->get();
    }


    private function getPropertyValueFeatures($courseOunitFeatures)
    {
        $propValues = $courseOunitFeatures->pluck('ouc_property_value')->toArray();
        return OucPropertyValue::whereIn('ouc_property_id', $propValues)->get();
    }

    private function getProperties($propValues)
    {
        $props = $propValues->pluck('ouc_property_id')->toArray();
        return OucProperty::whereIn('id', $props)->get();
    }

    public static function getUnitableHierarchy($unitType)
    {
        $hierarchy = [
            'Modules\OUnitMS\app\Models\StateOfc' => [
                'Modules\OUnitMS\app\Models\CityOfc',
                'Modules\OUnitMS\app\Models\DistrictOfc',
                'Modules\OUnitMS\app\Models\VillageOfc',
            ],
            'Modules\OUnitMS\app\Models\CityOfc' => [
                'Modules\OUnitMS\app\Models\DistrictOfc',
                'Modules\OUnitMS\app\Models\VillageOfc',
            ],
            'Modules\OUnitMS\app\Models\DistrictOfc' => [
                'Modules\OUnitMS\app\Models\VillageOfc',
            ],
            'Modules\OUnitMS\app\Models\VillageOfc' => [
                'Modules\OUnitMS\app\Models\VillageOfc',
            ],
        ];

        return $hierarchy[$unitType] ?? [];
    }


    private function CountingAllTheCourseContainers($course, $courseTargets, $propValues, $props)
    {
        $AllRectuitments = [];

        foreach ($courseTargets as $courseTarget) {
            // Get categories related to the course target
            $categories = TargetOunitCat::where('course_target_id', $courseTarget->id)->get();
            $ounitCats = $categories->pluck('ounit_cat_id')->toArray();
            $categoriesModel = OunitCategoryEnum::getModelsByValues($ounitCats);

            // Fetch the organization unit along with its descendants
            $parentUnit = OrganizationUnit::with('descendants')->find($courseTarget->parent_ounit_id);

            if ($parentUnit && $parentUnit->descendants) {
                $selectedUnitType = $parentUnit->unitable_type;

                // Initialize an empty array to hold valid unit types
                $validUnitTypes = [];

                // Loop through each category model and merge the valid unit types
                foreach ($categoriesModel as $category) {
                    // Get valid unit types for each category model and merge them into the validUnitTypes array
                    $validUnitTypes = array_merge($validUnitTypes, (array)$this->getUnitableHierarchy($category));
                }

                // Remove duplicates from the valid unit types array
                $validUnitTypes = array_unique($validUnitTypes);

                // Ensure descendants is a collection before filtering
                $filteredDescendants = collect($parentUnit->descendants)->filter(function ($descendant) use ($validUnitTypes) {
                    return in_array($descendant->unitable_type, $validUnitTypes);
                })->values(); // Reset array keys

                // Convert object to an array if needed
                $parentUnit->setRelation('descendants', $filteredDescendants);
            }


            //Props Degree and column


            $AllOunitIds = $parentUnit->descendants->pluck('id')->toArray();
            $courseTargets = $courseTargets->pluck('id')->toArray();


            $empFeature = CourseEmployeeFeature::with('propertyble')->where('course_target_id', $courseTarget->id)->get();

            $levelIds = $empFeature->where('propertyble_type', 'Modules\\HRMS\\app\\Models\\Level')
                ->pluck('propertyble_id')
                ->toArray();

            $positionIds = $empFeature->where('propertyble_type', 'Modules\\HRMS\\app\\Models\\Position')
                ->pluck('propertyble_id')
                ->toArray();

            $jobIds = $empFeature->where('propertyble_type', 'Modules\\HRMS\\app\\Models\\Job')
                ->pluck('propertyble_id')
                ->toArray();

// Query recruitmentScripts with optional filtering
            $recruitmentScripts = RecruitmentScript::query();

            if (!empty($levelIds)) {
                $recruitmentScripts->orWhereIn('level_id', $levelIds);
            }

            if (!empty($positionIds)) {
                $recruitmentScripts->orWhereIn('position_id', $positionIds);
            }

            if (!empty($jobIds)) {
                $recruitmentScripts->orWhereIn('job_id', $jobIds);
            }

            if (!empty($AllOunitIds)) {
                $recruitmentScripts->orWhereIn('organization_unit_id', $AllOunitIds);
            }

// Fetch results
            $recruitmentScripts = $recruitmentScripts->select('employee_id')->pluck('employee_id')->toArray();
            $AllRectuitments[] = $recruitmentScripts;
        }

        $flattenedArray = array_unique(array_merge(...$AllRectuitments));
        return count(array_values($flattenedArray));
    }

}
