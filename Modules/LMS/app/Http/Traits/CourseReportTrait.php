<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Models\ContentType;
use Modules\LMS\app\Models\Course;

trait CourseReportTrait
{
    public function CourseInfo($courseID)
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
            ->get();

        $durationAudio = $this->AudioDuration($courseID, $contentTypes);
        $durationVideo = $this->VideoDuration($courseID, $VidContentTypes);
        $sumAudio = $durationAudio->sum('total');
        $sumVideo = $durationVideo->sum('total');
        $totalDuration = $sumAudio + $sumVideo;
//        $completionPercentage = $this->completionPercentage($courseID, $studentID);


        return [
            'course' => $course->first(),
            'durationOfAudio' => $durationAudio->first(),
            'durationOfVideo' => $durationVideo->first(),
            'totalDuration' => $totalDuration,
//            'completionPercentage' => $completionPercentage,
        ];
    }


    public function AudioDuration($courseID, $contentTypes)
    {
        $course = Course::joinRelationship('chapters.lessons.contents.consumeLog')
            ->joinRelationship('chapters.lessons.contents.contentType')
            ->joinRelationship('chapters.lessons.contents.file')
            ->leftJoinRelationship('courseExams.exams.answerSheets')
            ->select([
                'files.duration as duration',
                'content_consume_log.consume_round as consume_round',
                'content_consume_log.consume_data as consume_data',
            ])
            ->where('courses.id', $courseID)
            ->where('content_type.id', $contentTypes)
            ->distinct()
            ->get();
        return $course->map(function ($item) {
            return [
                'duration' => $item->duration,
                'consume_round' => $item->consume_round,
                'total' => (($item->duration * $item->consume_round) + $item->consume_data),
            ];
        });

    }

    public function VideoDuration($courseID, $VidContentTypes)
    {
        $course = Course::joinRelationship('chapters.lessons.contents.consumeLog')
            ->joinRelationship('chapters.lessons.contents.contentType')
            ->joinRelationship('chapters.lessons.contents.file')
            ->leftJoinRelationship('courseExams.exams.answerSheets')
            ->select([
                'files.duration as duration',
                'content_consume_log.consume_round as consume_round',
                'content_consume_log.consume_data as consume_data',
            ])
            ->where('courses.id', $courseID)
            ->where('content_type.id', $VidContentTypes)
            ->distinct()
            ->get();

        return $course->map(function ($item) {
            return [
                'duration' => $item->duration,
                'consume_round' => $item->consume_round,
                'total' => (($item->duration * $item->consume_round) + $item->consume_data),
            ];
        });

    }

}
