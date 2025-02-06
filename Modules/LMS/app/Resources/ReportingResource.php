<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'studentInfo' => [
                'name' => $this['studentInfo']['name'],
                'poseName' => $this['studentInfo']['poseName'],
                'avatar' => $this['studentInfo']['avatar'],
            ],
            'answerSheetOfFinalExam' => [
                'score' => $this['answerSheetOfFinalExam']['score'],
                'statusName' => $this['answerSheetOfFinalExam']['statusName'],
                'startTime' => $this['answerSheetOfFinalExam']['start_date_time'],
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
                'durationOfAudio' => $this['courseInformation']['durationOfAudio'],
                'durationOfVideo' => $this['courseInformation']['durationOfVideo'],
                'totalDuration' => $this['courseInformation']['totalDuration'],
                'completionPercentage' => $this['courseInformation']['completionPercentage'],
            ],
            'practiceExam' => [
                'calculate' => $this['practiceExam']['calculate'],
                'answerSheetOfPracticalExam' => $this['practiceExam']['answerSheetOfPracticalExam'],
                'practicalExamEnrollment' => $this['practiceExam']['practicalExamEnrollment'],
            ],
        ];
    }
}
