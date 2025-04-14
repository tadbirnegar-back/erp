<?php

namespace Modules\LMS\app\Http\Traits;

use Illuminate\Support\Facades\Cache;
use Modules\LMS\app\Http\Enums\ContentStatusEnum;
use Modules\LMS\app\Http\Enums\LessonStatusEnum;
use Modules\LMS\app\Http\GlobalScope\ContentScope;
use Modules\LMS\app\Models\Chapter;
use Modules\LMS\app\Models\Content;
use Modules\LMS\app\Models\ContentConsumeLog;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\Lesson;
use Modules\LMS\app\Models\LessonStudyLog;
use Modules\LMS\app\Resources\SideBarCourseShowResource;

trait ContentTrait
{
    use LessonTrait, CourseTrait;

    public function storeContent($data)
    {
        $insertData = $this->prepareContentData($data);

        Content::insert($insertData);
    }

    private function prepareContentData($data)
    {
        $contents = json_decode($data['contents'], true);

        return array_map(function ($content) use ($data) {
            return [
                'lesson_id' => $data['lessonID'],
                'content_type_id' => $content['contentTypeID'],
                'file_id' => $content['contentFileID'],
                'name' => $content['contentName'],
                'status_id' => $this->contentActiveStatus()->id,
                'teacher_id' => $content['contentTeacherID'],
            ];
        }, $contents);
    }

    public function contentLogUpsert($data, $user)
    {
        $contentLog = ContentConsumeLog::where('content_id', $data['contentID'])
            ->where('student_id', $user->student->id)
            ->first();

        // Determine if it's a new entry or an update
        $isNewEntry = !$contentLog;

        return ContentConsumeLog::updateOrCreate(
            [
                'content_id' => $data['contentID'],
                'student_id' => $user->student->id,
            ],
            [
                'consume_data' => $data['consumeData'],
                'last_played' => $data['lastPlayed'],
                'set' => $data['set'],
                'create_date' => $isNewEntry ? now() : $contentLog->create_date,
                'last_modified' => now(),
            ]
        );
    }

    public function calculateRounds($data, $user)
    {
        $content = Content::query()
            ->leftJoinRelationshipUsingAlias('file', 'file_alias')
            ->select([
                'file_alias.duration as duration',
            ])->where('contents.id', $data['content_id'])->first();
        return $this->increseContentlogRound($data['consume_data'], $content->duration, $data['id'], $user);
    }

    public function increseContentlogRound($consume_secounds, $file_secounds, $logID, $user)
    {
        $log = ContentConsumeLog::with('content.lesson')->where('student_id', $user->student->id)->find($logID);

        $content = Content::query()
            ->leftJoinRelationship('lesson.lessonStudyLog', [
                'lesson' => fn($query) => $query->as('lesson_alias'),
                'lessonStudyLog' => fn($query) => $query->as('lesson_log_alias')
                    ->on('lesson_log_alias.lesson_id', 'lesson_alias.id')
                    ->where('student_id', $user->student->id)
            ])
            ->select([
                'lesson_alias.id as lesson_alias_id',
                'lesson_log_alias.id as lesson_log_alias_id',
                'lesson_log_alias.is_completed as lesson_log_alias_is_completed',
            ])->where('contents.id', $log['content_id'])->first();

        if ($consume_secounds + 1 > $file_secounds * 70 / 100) {
            $log->consume_round = $log->consume_round + 1;
            $log->consume_data = null;
            $log->is_complete = true;
            $log->set = null;
            $log->last_played = null;
            $log->save();

            if (isset($content->lesson_log_alias_id)) {

                $lessonLog = LessonStudyLog::find($content->lesson_log_alias_id);
                $lessonLog->study_count = $lessonLog->study_count + 1;
                $lessonLog->last_study_date = now();
                $lessonLog->is_completed = true;
                $lessonLog->student_id = $user->student->id;
                $lessonLog->save();
            } else {
                $lessonLog = $this->lessonLogCreate($content->lesson_alias_id, $user);
            }

            $chapter = ContentConsumeLog::with('content.lesson.chapter.course')->find($logID);
            $chapterId = $chapter->content->lesson->chapter->id;
            $allContentsOfChapter = Chapter::with('lessons')->find($chapterId);

            $lessons = $allContentsOfChapter->lessons->pluck('id')->toArray();

            $activeLessons = collect();
            $isCompletedAllLessons = true;

            foreach ($lessons as $lesson) {
                $lesson = Lesson::find($lesson);
                $lesson->load('chapter.course');

                $course = Course::find($lesson->chapter->course->id);

                //Enroll
                $enroll = $user->enrolls->where('course_id', $chapter->content->lesson->chapter->course->id)->first();
                $studyCount = $enroll->study_count;

                //log check
                $course->load(['allActiveLessons.lessonStudyLog' => function ($query) use ($user) {
                    $query->where('student_id', $user->student->id);
                }]);
                $isCompletedAllLessons = $course->allActiveLessons->every(function ($lesson) use ($studyCount) {
                    $lessonStudyLog = $lesson->lessonStudyLog->first(); // Ensure single instance
                    return $lessonStudyLog && $lessonStudyLog->is_completed == true && $lessonStudyLog->study_count > $studyCount;
                });
            }

            if ($isCompletedAllLessons) {
                $enroll = $user->enrolls->where('course_id', $chapter->content->lesson->chapter->course->id)->first();
                $enroll->study_completed = true;
                $enroll->study_count = $enroll->study_count + 1;
                if (is_null($enroll->first_completed_date)) {
                    $enroll->first_completed_date = now();
                }
                $enroll->last_completed_date = now();
                $enroll->save();
            }
        }
        return ["log" => $log];
    }

    public function checkLessonStatus($id)
    {
        $lesson = Lesson::with('latestStatus')->find($id);
        return $lesson->latestStatus;
    }


    public function deactiveContent($data)
    {
        $contentIDs = json_decode($data['deleteContent']);
        $statusId = $this->contentInActiveStatus()->id;

        Content::withoutGlobalScope(ContentScope::class)->whereIn('id', $contentIDs)->update(['status_id' => $statusId]);
    }

    public function contentActiveStatus()
    {
        return Cache::rememberForever('content_active_status', function () {
            return Content::GetAllStatuses()
                ->firstWhere('name', ContentStatusEnum::ACTIVE->value);
        });
    }

    public function contentInActiveStatus()
    {
        return Cache::rememberForever('content_inactive_status', function () {
            return Content::GetAllStatuses()
                ->firstWhere('name', ContentStatusEnum::IN_ACTIVE->value);
        });
    }
}
