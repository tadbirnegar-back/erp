<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportingResource extends JsonResource
{
    /**
     *
     * @param array $data
     */
    public function toArray($request): array
    {
        return [
            'studentInfo' => [
                'name' => data_get($this, 'studentInfo.name'),
                'poseName' => data_get($this, 'studentInfo.poseName'),
                'avatar' => data_get($this, 'studentInfo.avatar'),
            ],
            'answerSheetOfFinalExam' => [
                'score' => data_get($this, 'answerSheetOfFinalExam.score'),
                'statusName' => in_array(data_get($this, 'answerSheetOfFinalExam.statusName'), ['رد شده', null], true)
                    ? 'در انتظار امتحان'
                    : data_get($this, 'answerSheetOfFinalExam.statusName'),
                'startTime' => convertDateTimeGregorianToJalaliDateTime($this['answerSheetOfFinalExam']['start_date_time'] ?? null),
            ],
            'examResultListCount'=> data_get($this, 'finalExamEnrollment') > 0,
            'finalExamEnrollment' => data_get($this, 'finalExamEnrollment'),
            'FailedExams' => data_get($this, 'FailedExams'),

            'calculate' => [
                'correct' => data_get($this, 'calculate.correct'),
                'false' => data_get($this, 'calculate.false'),
                'null' => data_get($this, 'calculate.null'),
                'allQuestions' => data_get($this, 'calculate.allQuestions'),
            ],
            'courseInformation' => [
                'course' => data_get($this, 'courseInformation.course'),
                'durationOfAudio' => data_get($this, 'courseInformation.durationOfAudio'),
                'durationOfVideo' => data_get($this, 'courseInformation.durationOfVideo'),
                'totalDuration' => data_get($this, 'courseInformation.totalDuration'),
                'completionPercentage' => data_get($this, 'courseInformation.completionPercentage'),
                'enrolledCount' => data_get($this, 'courseInformation.erolled'),
            ],
            'practiceExam' => [
                'calculate' => data_get($this, 'practiceExam.calculate'),
                'answerSheetOfPracticalExam' => data_get($this, 'practiceExam.answerSheetOfPracticalExam'),
                'practicalExamEnrollment' => data_get($this, 'practiceExam.practicalExamEnrollment'),
                'scoreAverage' => data_get($this, 'practiceExam.scoreAverage'),
            ],
        ];
    }

}
