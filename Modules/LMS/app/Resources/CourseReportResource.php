<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'course' => [
                'cover' => $this['cover']['avatar'] ?? null,
                'courseTitle' => $this['course']['courseTitle'] ?? null,
                'chapters_count' => $this['course']['chapters_count'] ?? null,
                'all_active_lessons_count' => $this['course']['all_active_lessons_count'] ?? null,
            ],
            'StudentsCount' => $this['allStudents'] ?? null,
            'certificatesCount' => $this['certificatesCount'] ?? null,
            'scoreAverageAndEnrolledStudents' => [
                'average' => $this['scoreAverageAndEnrolledStudents']['average'] ?? null,
                'EnrolledStudents' => $this['scoreAverageAndEnrolledStudents']['EnrolledStudents'] ?? null,
            ],
            'durationOfAudio' => [
                'duration' => $this['durationOfAudio']['duration'] ?? null,
                'consume_round' => $this['durationOfAudio']['consume_round'] ?? null,
                'total' => $this['durationOfAudio']['total'] ?? null,
                'averageOfAudio' => $this['durationOfAudio']['averageOfAudio'] ?? null,
            ],
            'durationOfVideo' => [
                'duration' => $this['durationOfVideo']['duration'] ?? null,
                'consume_round' => $this['durationOfVideo']['consume_round'] ?? null,
                'total' => $this['durationOfVideo']['total'] ?? null,
                'averageOfVideo' => $this['durationOfVideo']['averageOfVideo'] ?? null,
            ],
            'totalDuration' => ($this['durationOfAudio']['duration'] ?? 0) + ($this['durationOfVideo']['duration'] ?? 0),

            'totalPlayedDuration' => $this['totalPlayedDuration'] ?? null,
            'subInfoOfAllEnrollmentStudents' => [
                'approvedStudents' => $this['approvedStudents'] ?? null,
                'declinedStudents' => $this['declinedStudents'] ?? null,
            ],
            'totalStudyDurationAverage' => $this['totalStudyDurationAverage'] ?? null,
            'subInfoOfAllStudents' => [
                'includedStudents' => 100,
                'hasNotParticipated' => 50,
                'studyCompleted' => $this['subCount']['studyCompleted'] ?? null,
                'isStudying' => $this['subCount']['isStudying'] ?? null,
            ],
            'scoreAndMonthChart' => array_map(function ($item) {
                return [
                    'month' => $item['month'] ?? null,
                    'average_score' => $item['average_score'] ?? null,
                ];
            }, $this['scoreAndMonthChart'] ?? []),

        ];
    }


}
