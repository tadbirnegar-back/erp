<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportingResource extends JsonResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->baseUrl = url('/') . '/'; // Initialize base URL
    }

    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {

        return [

            'studentInfo' => [
                'name' => $this['studentInfo']['name'],
                'poseName' => $this['studentInfo']['poseName'],
                'avatar' => ($this['studentInfo']['avatar']),
            ],
            'answerSheetOfFinalExam' => [
                'score' => $this['answerSheetOfFinalExam']['score'],
                'statusName' => $this['answerSheetOfFinalExam']['statusName'] === 'رد شده'
                    ? 'در انتظار امتحان'
                    : $this['answerSheetOfFinalExam']['statusName'],
                'startTime' => convertDateTimeGregorianToJalaliDateTime($this['answerSheetOfFinalExam']['start_date_time']),
            ],
            'finalExamEnrollment' => $this['finalExamEnrollment'],
            'FailedExams' => $this['FailedExams'],

            'calculate' => [
                'correct' => $this['calculate']['correct'],
                'false' => $this['calculate']['false'],
                'null' => $this['calculate']['null'],
                'allQuestions' => $this['calculate']['allQuestions'],
            ],
            'courseInformation' => [
                'course' => $this['courseInformation']['course'],
                'activeLessonsCount' => $this['courseInformation']['activeLessonsCount'],
                'durationOfAudio' => $this['courseInformation']['durationOfAudio'],
                'durationOfVideo' => $this['courseInformation']['durationOfVideo'],
                'totalDuration' => $this['courseInformation']['totalDuration'],
                'completionPercentage' => $this['courseInformation']['completionPercentage'],
                'erolledCount' => $this['courseInformation']['erolled'],
            ],
            'practiceExam' => [
                'calculate' => $this['practiceExam']['calculate'],
                'answerSheetOfPracticalExam' => $this['practiceExam']['answerSheetOfPracticalExam'],
                'practicalExamEnrollment' => $this['practiceExam']['practicalExamEnrollment'],
                'scoreAverage' => $this['practiceExam']['scoreAverage'],
            ],
        ];
    }
}
