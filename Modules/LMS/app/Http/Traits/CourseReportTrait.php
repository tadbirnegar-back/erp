<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Http\Enums\RepositoryEnum;
use Modules\LMS\app\Models\Content;
use Modules\LMS\app\Models\ContentType;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\Question;
use Modules\LMS\app\Models\Repository;

trait CourseReportTrait
{
    public function CourseInfo($courseID)
    {
//        ContentTypeEnum::AUDIO
//        ContentTypeEnum::VIDEOv
        $contentTypes = ContentType::where('name', 'صوتی')->first()->id;
        $VidContentTypes = ContentType::where('name', 'تصویری')->first()->id;
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

        $sumStudyAudio = $durationAudio->sum('duration');
        $sumStudyVideo = $durationVideo->sum('duration');
        $totalStudyDuration = ($sumStudyAudio + $sumStudyVideo) / 2;


        return [
            'course' => $course->first(),
            'durationOfAudio' => $durationAudio->first(),
            'durationOfVideo' => $durationVideo->first(),
            'totalPlayedDuration' => $totalDuration,
        ];
    }


    public function AudioDuration($courseID, $contentTypes)
    {
        $contentStatus = $this->activeContentStatus()->id;
        $course = Course::joinRelationship('chapters.lessons.contents.consumeLog')
            ->joinRelationship('chapters.lessons.contents.contentType')
            ->leftJoinRelationship('chapters.lessons.contents.file')
            ->select([
                'files.duration as duration',
                'content_consume_log.consume_round as consume_round',
                'content_consume_log.consume_data as consume_data',
                'files.id as fileID',
            ])
            ->where('courses.id', $courseID)
            ->where('content_type.id', $contentTypes)
            ->where('contents.status_id', $contentStatus)
            ->distinct()
            ->get();

        return $course->map(function ($item) {
            return [
                'test' => $item,
                'duration' => $item->duration,
                'consume_round' => $item->consume_round,
                'total' => (($item->duration * $item->consume_round) + $item->consume_data),
            ];
        });

    }

    public function VideoDuration($courseID, $VidContentTypes)
    {
        $contentStatus = $this->activeContentStatus()->id;

        $course = Course::joinRelationship('chapters.lessons.contents.consumeLog')
            ->joinRelationship('chapters.lessons.contents.contentType')
            ->joinRelationship('chapters.lessons.contents.file')
            ->select([
                'files.duration as duration',
                'content_consume_log.consume_round as consume_round',
                'content_consume_log.consume_data as consume_data',
                'files.id as fileID',
            ])
            ->where('courses.id', $courseID)
            ->where('content_type.id', $VidContentTypes)
            ->where('contents.status_id', $contentStatus)
            ->distinct()
            ->get();
//        dd($course->count());

        return $course->map(function ($item) {
            return [
                'test' => $item,
                'duration' => $item->duration,
                'consume_round' => $item->consume_round,
                'total' => (($item->duration * $item->consume_round) + $item->consume_data),
            ];
        });

    }

    public function activeContentStatus()
    {
        return Content::GetAllStatuses()->firstWhere('name', '=', 'فعال');
    }

    public function ans($courseID)
    {
        $repo = Repository::where('name', RepositoryEnum::FINAL->value)->first()->id;
        return Question::joinRelationship('answers.answerSheet.status')
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
            ->orderBy('answer_sheets.start_date_time', 'desc')
            ->first();
//        $scoreAvrage=
    }


}
