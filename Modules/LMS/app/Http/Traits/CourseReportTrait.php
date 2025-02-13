<?php

namespace Modules\LMS\app\Http\Traits;

use DB;
use Modules\EMS\app\Http\Traits\DateTrait;
use Modules\LMS\app\Http\Enums\ContentTypeEnum;
use Modules\LMS\app\Http\Enums\RepositoryEnum;
use Modules\LMS\app\Models\AnswerSheet;
use Modules\LMS\app\Models\ContentType;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\Question;
use Modules\LMS\app\Models\Repository;
use Morilog\Jalali\Jalalian;

trait CourseReportTrait
{
    use AnswerSheetTrait, ContentTrait, DateTrait, LessonTrait;

    public function CourseInfo($courseID)
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

        $durationAudio = $this->AudioDuration($courseID, $contentTypes);
        $durationVideo = $this->VideoDuration($courseID, $VidContentTypes);
        $sumAudio = $durationAudio['total'];
        $sumVideo = $durationVideo['total'];
        $totalDuration = $sumAudio + $sumVideo;

        $sumStudyAudio = $durationAudio->sum('duration');
        $sumStudyVideo = $durationVideo->sum('duration');
        $totalStudyDuration = ($sumStudyAudio + $sumStudyVideo) / 2;
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
            'scoreAndMonthChart' => $months
        ];
    }


    public function AudioDuration($courseID, $contentTypes)
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
        return [
            'duration' => $totalDuration,
            'consume_round' => $totalConsumeRound,
            'total' => ($totalOverall == 0) ? null : $totalOverall,
        ];

    }

    public function VideoDuration($courseID, $VidContentTypes)
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
        return [
            'duration' => $totalDuration,
            'consume_round' => $totalConsumeRound,
            'total' => ($totalOverall == 0) ? null : $totalOverall,
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
