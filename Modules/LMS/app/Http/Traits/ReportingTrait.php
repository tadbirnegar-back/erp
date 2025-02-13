<?php

namespace Modules\LMS\app\Http\Traits;

use DB;
use Modules\AAA\app\Models\User;
use Modules\LMS\app\Http\Enums\ContentTypeEnum;
use Modules\LMS\app\Http\Enums\RepositoryEnum;
use Modules\LMS\app\Models\AnswerSheet;
use Modules\LMS\app\Models\ContentType;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\Question;
use Modules\LMS\app\Models\Repository;
use Morilog\Jalali\Jalalian;

trait ReportingTrait
{
    use AnswerSheetTrait, LessonTrait, ContentTrait;


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

    public function correct($optionID, $repo)
    {
        return Question::joinRelationship('repository')
            ->joinRelationship('options')
            ->whereIn('options.id', $optionID)
            ->where('is_correct', 1)
            ->where('repositories.id', $repo)
            ->count();
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
        $course = Course::joinRelationship('chapters.lessons.contents.file')
            ->leftJoinRelationship('courseExams.exams.answerSheets')
            ->joinRelationship('chapters.lessons.contents.contentType')
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

        $latestStatusSubquery = DB::table('status_lesson')
            ->select('lesson_id')
            ->where('status_id', $lessonActiveStatus)
            ->whereIn('created_date', function ($query) {
                $query->selectRaw('MAX(created_date)')
                    ->from('status_lesson')
                    ->groupBy('lesson_id');
            });

        $course = Course::leftJoinRelationship('chapters.lessons.contents.consumeLog', [
            'consumeLog' => fn($join) => $join->on('content_consume_log.student_id', DB::raw("'" . $studentID . "'")),
        ])
            ->joinRelationship('chapters.lessons.contents.contentType')
            ->joinRelationship('chapters.lessons.contents.file')
            ->joinRelationship('chapters.lessons.lessonStatus')
            ->select([
                'files.duration as duration',
                'content_consume_log.consume_round as consume_round',
                'content_consume_log.consume_data as consume_data',
            ])
            ->where('courses.id', $courseID)
            ->where('content_type.id', $contentTypes)
            ->where('contents.status_id', $contentStatus)
            ->whereIn('status_lesson.lesson_id', $latestStatusSubquery)
            ->distinct()
            ->get();
        $totalDuration = $course->sum(function ($item) {
            return $item->duration == 0 ? null : $item->duration;
        });

        $totalConsumeRound = $course->sum(function ($item) {
            return $item->consume_round == 0 ? null : $item->consume_round;
        });

        $totalConsumeData = $course->sum(function ($item) {
            return $item->consume_data == 0 ? null : $item->consume_data;
        });
        $totalOverall = ($totalDuration * $totalConsumeRound) + $totalConsumeData;
        return [
            'duration' => $totalDuration,
            'consume_round' => $totalConsumeRound,
            'total' => ($totalOverall == 0) ? null : $totalOverall,
        ];

    }

    public function VideoDuration($studentID, $courseID, $VidContentTypes)
    {
        $lessonActiveStatus = $this->lessonActiveStatus()->id;
        $contentStatus = $this->contentActiveStatus()->id;

        $latestStatusSubquery = DB::table('status_lesson')
            ->select('lesson_id')
            ->where('status_id', $lessonActiveStatus)
            ->whereIn('created_date', function ($query) {
                $query->selectRaw('MAX(created_date)')
                    ->from('status_lesson')
                    ->groupBy('lesson_id');
            });

        $course = Course::leftJoinRelationship('chapters.lessons.contents.consumeLog', [
            'consumeLog' => fn($join) => $join->on('content_consume_log.student_id', DB::raw("'" . $studentID . "'")),
        ])
            ->joinRelationship('chapters.lessons.contents.contentType')
            ->leftJoinRelationship('chapters.lessons.contents.file')
            ->joinRelationship('chapters.lessons.lessonStatus')
            ->select([
                'files.duration as duration',
                'content_consume_log.consume_round as consume_round',
                'content_consume_log.consume_data as consume_data',
            ])
            ->where('courses.id', $courseID)
            ->where('content_type.id', $VidContentTypes)
            ->where('contents.status_id', $contentStatus)
            ->whereIn('status_lesson.lesson_id', $latestStatusSubquery)
            ->distinct()
            ->get();

        $totalDuration = $course->sum(function ($item) {
            return $item->duration == 0 ? null : $item->duration;
        });

        $totalConsumeRound = $course->sum(function ($item) {
            return $item->consume_round == 0 ? null : $item->consume_round;
        });

        $totalConsumeData = $course->sum(function ($item) {
            return $item->consume_data == 0 ? null : $item->consume_data;
        });
        $totalOverall = ($totalDuration * $totalConsumeRound) + $totalConsumeData;
        return [
            'duration' => $totalDuration,
            'consume_round' => $totalConsumeRound,
            'total' => ($totalOverall == 0) ? null : $totalOverall,
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

    public function correctQuestionAnswers($optionID, $practiceRepo)
    {
        return Question::joinRelationship('repository')
            ->joinRelationship('options')
            ->whereIn('options.id', $optionID)
            ->where('is_correct', 1)
            ->where('repositories.id', $practiceRepo)
            ->count();
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
        ];
    }


    public function AudioCourseDuration($courseID, $contentTypes)
    {
        $lessonActiveStatus = $this->lessonActiveStatus()->id;
        $contentStatus = $this->contentActiveStatus()->id;

        $latestStatusSubquery = DB::table('status_lesson')
            ->select('lesson_id')
            ->where('status_id', $lessonActiveStatus)
            ->whereIn('created_date', function ($query) {
                $query->selectRaw('MAX(created_date)')
                    ->from('status_lesson')
                    ->groupBy('lesson_id');
            });

        $course = Course::leftJoinRelationship('chapters.lessons.contents.consumeLog')
            ->joinRelationship('chapters.lessons.contents.contentType')
            ->joinRelationship('chapters.lessons.contents.file')
            ->joinRelationship('chapters.lessons.lessonStatus')
            ->select([
                'files.duration as duration',
                'content_consume_log.consume_round as consume_round',
                'content_consume_log.consume_data as consume_data',
            ])
            ->where('courses.id', $courseID)
            ->where('content_type.id', $contentTypes)
            ->where('contents.status_id', $contentStatus)
            ->whereIn('status_lesson.lesson_id', $latestStatusSubquery)
            ->distinct()
            ->get();
        $totalDuration = $course->sum(function ($item) {
            return $item->duration == 0 ? null : $item->duration;
        });

        $totalConsumeRound = $course->sum(function ($item) {
            return $item->consume_round == 0 ? null : $item->consume_round;
        });

        $totalConsumeData = $course->sum(function ($item) {
            return $item->consume_data == 0 ? null : $item->consume_data;
        });
        $totalOverall = ($totalDuration * $totalConsumeRound) + $totalConsumeData;
        $allStudentsCount = $this->allStudentsCount($courseID);

        $average = $allStudentsCount > 0 ? $totalDuration / $allStudentsCount : 0;
        return [
            'duration' => $totalDuration,
            'consume_round' => $totalConsumeRound,
            'total' => ($totalOverall == 0) ? null : $totalOverall,
            'averageOfAudio' => $average,
        ];

    }

    public function VideoCourseDuration($courseID, $VidContentTypes)
    {
        $lessonActiveStatus = $this->lessonActiveStatus()->id;
        $contentStatus = $this->contentActiveStatus()->id;

        $latestStatusSubquery = DB::table('status_lesson')
            ->select('lesson_id')
            ->where('status_id', $lessonActiveStatus)
            ->whereIn('created_date', function ($query) {
                $query->selectRaw('MAX(created_date)')
                    ->from('status_lesson')
                    ->groupBy('lesson_id');
            });

        $course = Course::leftJoinRelationship('chapters.lessons.contents.consumeLog')
            ->joinRelationship('chapters.lessons.contents.contentType')
            ->leftJoinRelationship('chapters.lessons.contents.file')
            ->joinRelationship('chapters.lessons.lessonStatus')
            ->select([
                'files.duration as duration',
                'content_consume_log.consume_round as consume_round',
                'content_consume_log.consume_data as consume_data',
            ])
            ->where('courses.id', $courseID)
            ->where('content_type.id', $VidContentTypes)
            ->where('contents.status_id', $contentStatus)
            ->whereIn('status_lesson.lesson_id', $latestStatusSubquery)
            ->distinct()
            ->get();

        $totalDuration = $course->sum(function ($item) {
            return $item->duration == 0 ? null : $item->duration;
        });

        $totalConsumeRound = $course->sum(function ($item) {
            return $item->consume_round == 0 ? null : $item->consume_round;
        });

        $totalConsumeData = $course->sum(function ($item) {
            return $item->consume_data == 0 ? null : $item->consume_data;
        });
        $totalOverall = ($totalDuration * $totalConsumeRound) + $totalConsumeData;
        $allStudentsCount = $this->allStudentsCount($courseID);

        $average = $allStudentsCount > 0 ? $totalDuration / $allStudentsCount : 0;
        return [
            'duration' => $totalDuration,
            'consume_round' => $totalConsumeRound,
            'total' => ($totalOverall == 0) ? null : $totalOverall,
            'averageOfVideo' => $average,
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
            ->distinct()
            ->orderBy('answer_sheets.score', 'desc');
        return [
            'average' => $ans->average('answer_sheets.score'),
            'EnrolledStudents' => $ans->count('answer_sheets.student_id'),
            'scores' => $ans->get()->pluck('scores'),
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
            ->distinct()
            ->get();
        return $count->count();
    }

    public function countAnswerSheetDeclinedStatusOfStudents($courseID)
    {
        $repo = Repository::where('name', RepositoryEnum::FINAL->value)->first()->id;
        $declinedStatus = $this->answerSheetDeclinedStatus()->id;
        $count = AnswerSheet::joinRelationship('answers.questions')
            ->joinRelationship('status')
            ->joinRelationship('exam.courseExams.course', [
                'course' => fn($join) => $join->as('course_alias'),
                'exam' => fn($join) => $join->as('exam_alias')
            ])
            ->joinRelationship('repository')
            ->where('repositories.id', $repo)
            ->where('answer_sheets.status_id', $declinedStatus)
            ->where('course_alias.id', $courseID)
            ->distinct()
            ->get();
        return $count->count();
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
        $query = Course::joinRelationship('courseExams.exams.answerSheets')
            ->select([
                'answer_sheets.score as scores',
                'answer_sheets.finish_date_time as finish_date_time',
            ])
            ->where('courses.id', $courseID)
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


}
