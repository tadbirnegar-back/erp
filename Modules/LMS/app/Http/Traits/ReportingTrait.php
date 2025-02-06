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
        $mostRecentAnswerSheet = Question::joinRelationship('answers.answerSheet.status')
            ->joinRelationship('answers.answerSheet.exam.courseExams.course')
            ->joinRelationship('repository')
            ->select([
                'answer_sheets.start_date_time',
                'answer_sheets.student_id as studentID',
                'answer_sheets.status_id as statusID',
                'statuses.name as statusName',
                'answer_sheets.score as score',
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
        $examFinalCount = $this->examFinalCount($studentID, $courseID, $repo);
        $examPracticeCount = $this->practicalExam($answerSheetID, $user, $data, $courseID);
        $optionID = array_filter(array_column($data['questions'], 'option_id'));
        $calculate = $this->counting($optionID, $answerSheetID, $examId);
        $failedExams = $this->FailedExams($studentID, $courseID, $repo);
        $courseInfo = $this->courseInfo($studentID, $courseID);


        return [
            'calculate' => $calculate,
            'answerSheetOfFinalExam' => $mostRecentAnswerSheet,
            'studentInfo' => $studentInfo,
            'finalExamEnrollment' => $examFinalCount,
            'practiceExamEnrollment' => $examPracticeCount,
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
            ->joinRelationship('status')
            ->joinRelationship('repository')
            ->select([
                'answer_sheets.id as answerSheetID',
                'exams.title as examTitle',
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
            ->distinct()
            ->get();

        $durationAudio = $this->AudioDuration($studentID, $courseID, $contentTypes);
        $durationVideo = $this->VideoDuration($studentID, $courseID, $VidContentTypes);
        $sumAudio = $durationAudio->sum('total');
        $sumVideo = $durationVideo->sum('total');
        $totalDuration = $sumAudio + $sumVideo;
        $completionPercentage = $this->completionPercentage($courseID, $studentID);

        return [
            'course' => $course,
            'durationOfAudio' => $durationAudio,
            'durationOfVideo' => $durationVideo,
            'totalDuration' => $totalDuration / 60,
            'completionPercentage' => $completionPercentage,
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
                'duration' => $item->duration / 60,
                'consume_round' => $item->consume_round / 60,
                'total' => (($item->duration * $item->consume_round) + $item->consume_data) / 60,
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
                'duration' => $item->duration / 60,
                'consume_round' => $item->consume_round / 60,
                'total' => (($item->duration * $item->consume_round) + $item->consume_data) / 60,
            ];
        });

    }

    public function practicalExam($answerSheetID, $user, $data, $courseID)
    {
//        $repo = Repository::where('name', RepositoryEnum::FINAL->value)->first()->id;
        $practiceRepo = Repository::where('name', RepositoryEnum::PRACTICE->value)->first()->id;
        $studentID = $user->student->id;
        $AnswerSheet = Question::joinRelationship('answers.answerSheet.exam.courseExams.course')
            ->joinRelationship('status')
            ->joinRelationship('repository')
            ->select([
                'answer_sheets.exam_id as examID',
                'answer_sheets.start_date_time as startTime',
                'answer_sheets.score as score',
            ])
            ->where('repositories.id', $practiceRepo)
            ->where('courses.id', $courseID)
            ->where('answer_sheets.student_id', $studentID)
            ->get();
        $examId = $AnswerSheet->pluck('examID')->toArray();
        $startDate = $AnswerSheet->pluck('startTime')->toArray();
        $examCount = $this->examPracticalCount($studentID, $courseID, $practiceRepo);
        $optionID = array_filter(array_column($data['questions'], 'option_id'));
        $calculate = $this->count($optionID, $answerSheetID, $examId, $practiceRepo);
        return [
            'calculate' => $calculate,
            'answerSheetOfPracticalExam' => $AnswerSheet,
            'startDate' => $startDate,
            'ExamEnrollment' => $examCount,
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
            ->get()
            ->count();
        return $countExams;
    }

    public function count($optionID, $answerSheetID, $examId, $practiceRepo)
    {
        $correctAnswers = $this->correctQuestionAnswers($optionID, $practiceRepo);
        $falseAnswers = $this->inCorrectAnswers($optionID, $practiceRepo);
        $nullAnswers = $this->nullAnswers($answerSheetID);
        $score = $this->score($examId, $optionID);
        $questionCount = $this->questionsCount($examId, $practiceRepo);

        return [
            'score' => $score,
            'correct' => $correctAnswers,
            'false' => $falseAnswers,
            'null' => $nullAnswers,
            'allQuestions' => $questionCount,
        ];
    }

    public function questionsCount($examId, $practiceRepo)
    {
        return Question::joinRelationship('repository')
            ->joinRelationship('questionExams')
            ->where('exam_id', $examId)
            ->where('repositories.id', $practiceRepo)
            ->count('question_id');
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
            ->select(['lesson_study_log.is_completed as is_completed'])
            ->where('courses.id', $courseID)
            ->get();

        $totalLessons = $completion->count();
        $completedLessons = $completion->where('is_completed', 1)->count();

        $completionPercentage = ($totalLessons > 0) ? ($completedLessons / $totalLessons) * 100 : 0;

        return $completionPercentage;
    }

}
