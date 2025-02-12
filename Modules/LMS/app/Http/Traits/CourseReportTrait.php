<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\EMS\app\Http\Traits\DateTrait;
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
//        ContentTypeEnum::AUDIO
//        ContentTypeEnum::VIDEOv
        $contentTypes = ContentType::where('name', 'صوتی')->first()->id;
        $VidContentTypes = ContentType::where('name', 'تصویری')->first()->id;
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
        $sumAudio = $durationAudio->sum('total');
        $sumVideo = $durationVideo->sum('total');
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
            'durationOfAudio' => $durationAudio->first(),
            'durationOfVideo' => $durationVideo->first(),
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
        $contentStatus = $this->activeContentStatus()->id;
        $course = Course::leftJoinRelationship('chapters.lessons.contents.consumeLog')
            ->joinRelationship('chapters.lessons.contents.contentType')
            ->joinRelationship('chapters.lessons.contents.file')
            ->select([
                'files.duration as duration',
                'content_consume_log.consume_round as consume_round',
                'content_consume_log.consume_data as consume_data',
            ])
            ->where('courses.id', $courseID)
            ->where('content_type.id', $contentTypes)
            ->where('contents.status_id', $contentStatus)
            ->distinct()
            ->get();
        return $course->map(function ($item) {
            $total = ($item->duration * $item->consume_round) + $item->consume_data;
            return [
                'duration' => $item->duration,
                'consume_round' => $item->consume_round,
                'total' => ($total == 0) ? null : $total,
            ];
        });


    }

    public function VideoDuration($courseID, $VidContentTypes)
    {
        $contentStatus = $this->activeContentStatus()->id;
        $course = Course::leftJoinRelationship('chapters.lessons.contents.consumeLog')
            ->joinRelationship('chapters.lessons.contents.contentType')
            ->leftJoinRelationship('chapters.lessons.contents.file')
            ->select([
                'files.duration as duration',
                'content_consume_log.consume_round as consume_round',
                'content_consume_log.consume_data as consume_data',
            ])
            ->where('courses.id', $courseID)
            ->where('content_type.id', $VidContentTypes)
            ->where('contents.status_id', $contentStatus)
            ->distinct()
            ->get();

        return $course->map(function ($item) {
            $total = ($item->duration * $item->consume_round) + $item->consume_data;
            return [
                'duration' => $item->duration,
                'consume_round' => $item->consume_round,
                'total' => ($total == 0) ? null : $total,
            ];
        });

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
        // Fetch data from the database
        $query = Course::joinRelationship('courseExams.exams.answerSheets')
            ->select([
                'answer_sheets.score as scores',
                'answer_sheets.finish_date_time as finish_date_time',
            ])
            ->where('courses.id', $courseID)
            ->get();

        // Group scores by Persian month name
        $groupedData = $query->groupBy(function ($item) {
            // Convert Gregorian date to Persian date
            $persianDate = Jalalian::fromDateTime($item->finish_date_time);

            // Extract Persian month number (1-12)
            $monthNumber = $persianDate->getMonth();

            // Convert month number to Persian month name using humanReadableDate
            return $this->humanReadableDate($monthNumber);
        });

        // Prepare the result
        $result = [];
        foreach ($groupedData as $monthName => $items) {
            // Extract scores for this month
            $scores = $items->pluck('scores')->toArray();

            // Add to the result
            $result[] = [
                'month' => $monthName,
                'scores' => $scores,
            ];
        }

        return $result;
    }


}
