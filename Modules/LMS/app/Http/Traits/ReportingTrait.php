<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Http\Enums\ContentTypeEnum;
use Modules\LMS\app\Http\Enums\RepositoryEnum;
use Modules\LMS\app\Models\AnswerSheet;
use Modules\LMS\app\Models\ContentType;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\Question;
use Modules\LMS\app\Models\Repository;

trait ReportingTrait
{
    use AnswerSheetTrait;


    public function ans($answerSheetID, $user, $data, $courseID)
    {
        $repo = Repository::where('name', RepositoryEnum::FINAL->value)->first()->id;
        $studentID = $user->student->id;
        $mostRecentAnswerSheet = Question::joinRelationship('answers.answerSheet.exam.courseExams.course')
            ->joinRelationship('status')
            ->joinRelationship('repository')
            ->select([
                'answer_sheets.start_date_time as startTime',
                'answer_sheets.student_id as studentID',
                'answer_sheets.status_id as statusID',
                'answer_sheets.score as score',
                'statuses.name as statusName',
                'answer_sheets.exam_id as examID',
                'answer_sheets.id as answerSheetID',
                'repositories.id as repoID',
            ])
            ->where('repositories.id', $repo)
            ->where('courses.id', $courseID)
            ->where('answer_sheets.student_id', $studentID)
            ->orderBy('answer_sheets.start_date_time', 'desc')
            ->first();

        if (!$mostRecentAnswerSheet) {
            return response()->json(['error' => 'No answer sheets found'], 404);
        }

        $studentInfo = $this->student($user);
        $examId = $mostRecentAnswerSheet->examID;
        $examCount = $this->examCount($studentID, $courseID);
        $optionID = array_filter(array_column($data['questions'], 'option_id'));
        $startDate = $mostRecentAnswerSheet->startTime;
        $calculate = $this->counting($optionID, $answerSheetID, $examId);
        $failedExams = $this->FailedExams($studentID, $courseID, $repo);
        $courseInfo = $this->courseInfo($studentID, $courseID);


        return [
            'calculate' => $calculate,
            'answerSheet' => $mostRecentAnswerSheet,
            'studentInfo' => $studentInfo,
            'startDate' => $startDate,
            'ExamEnrollment' => $examCount,
            'FailedExams' => $failedExams,
            'courseInformation' => $courseInfo,
        ];
    }


    public function counting($optionID, $answerSheetID, $examId)
    {
        $correctAnswers = $this->correctAnswers($optionID);
        $falseAnswers = $this->falseAnswers($optionID);
        $nullAnswers = $this->nullAnswers($answerSheetID);
        $score = $this->score($examId, $optionID);
        $questionCount = $this->questionCount($examId);

        return [
            'score' => $score,
            'correct' => $correctAnswers,
            'false' => $falseAnswers,
            'null' => $nullAnswers,
            'allQuestions' => $questionCount,
        ];
    }

    public function examCount($studentID, $courseID)
    {
        $countExams = AnswerSheet::joinRelationship('exam.courseExams.course')
            ->joinRelationship('exam.courseExams')
            ->select([
                'answer_sheets.student_id as studentID',
                'answer_sheets.exam_id as examID',
                'courses.id as courseID',
            ])
            ->where('answer_sheets.student_id', $studentID)
            ->where('courses.id', $courseID)
            ->get()
            ->count();
        return $countExams;
    }

    public function FailedExams($studentID, $courseID, $repo)
    {
        $failedExams = Question::joinRelationship('answers.answerSheet.exam.courseExams.course')
            ->joinRelationship('status')
            ->joinRelationship('repository')
            ->select([
                'answer_sheets.id as answerSheetID',
                'exams.title as examTitle',
            ])
            ->where('repositories.id', $repo)
            ->where('courses.id', $courseID)
            ->where('answer_sheets.student_id', $studentID)
            ->where('answer_sheets.status_id', $this->answerSheetDeclinedStatus()->id)
            ->distinct()
            ->get()
            ->count();
        return $failedExams;
    }


    public function CourseInfo($studentID, $courseID)
    {
        $contentTypes = ContentType::where('name', ContentTypeEnum::AUDIO)->first()->id;
        $VidContentTypes = ContentType::where('name', ContentTypeEnum::VIDEO)->first()->id;
        $course = Course::joinRelationship('chapters.lessons.contents.consumeLog')
            ->joinRelationship('chapters.lessons.contents.file')
            ->joinRelationship('courseExams.exams.answerSheets')
            ->joinRelationship('chapters.lessons.contents.contentType')
            ->select([
                'courses.title as courseTitle',

            ])
            ->withCount('chapters', 'lessons')
            ->where('courses.id', $courseID)
            ->where('answer_sheets.student_id', $studentID)
//            ->where('content_type.id', $contentTypes)
            ->distinct()
            ->get();

        $durationAudio = $this->AudioDuration($studentID, $courseID, $contentTypes);
        $durationVideo = $this->VideoDuration($studentID, $courseID, $VidContentTypes);

        return [
            'course' => $course,
            'durationOfAudio' => $durationAudio,
            'durationOfVideo' => $durationVideo,
        ];

    }

    public function AudioDuration($studentID, $courseID, $contentTypes)
    {
        $course = Course::joinRelationship('chapters.lessons.contents.consumeLog')
            ->joinRelationship('chapters.lessons.contents.contentType')
            ->joinRelationship('chapters.lessons.contents.file')
            ->joinRelationship('courseExams.exams.answerSheets')
            ->select([
                'files.duration as duration',
                'content_consume_log.consume_round as consume_round',
                'content_consume_log.consume_data as consume_data',
            ])
            ->where('courses.id', $courseID)
            ->where('answer_sheets.student_id', $studentID)
            ->where('content_type.id', $contentTypes)
            ->distinct()
            ->get();
        return $course->map(function ($item) {
            return [
                'duration' => $item->duration,
                'consume_round' => $item->consume_round,
                'consume_data' => $item->consume_data,
                'total' => ($item->duration * $item->consume_round) + $item->consume_data,
            ];
        });

    }

    public function VideoDuration($studentID, $courseID, $VidContentTypes)
    {
        $course = Course::joinRelationship('chapters.lessons.contents.consumeLog')
            ->joinRelationship('chapters.lessons.contents.contentType')
            ->joinRelationship('chapters.lessons.contents.file')
            ->joinRelationship('courseExams.exams.answerSheets')
            ->select([
                'files.duration as duration',
                'content_consume_log.consume_round as consume_round',
                'content_consume_log.consume_data as consume_data',
            ])
            ->where('courses.id', $courseID)
            ->where('answer_sheets.student_id', $studentID)
            ->where('content_type.id', $VidContentTypes)
            ->distinct()
            ->get();
        return $course->map(function ($item) {
            return [
                'duration' => $item->duration,
                'consume_round' => $item->consume_round,
                'consume_data' => $item->consume_data,
                'total' => ($item->duration * $item->consume_round) + $item->consume_data,
            ];
        });

    }

}
