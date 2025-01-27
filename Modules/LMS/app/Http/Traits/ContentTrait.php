<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\App\Http\Enums\ContentStatusEnum;
use Modules\LMS\app\Http\GlobalScope\ContentScope;
use Modules\LMS\app\Models\Content;
use Modules\LMS\app\Models\ContentConsumeLog;
use Modules\LMS\app\Models\LessonStudyLog;

trait ContentTrait
{
    use LessonTrait;

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
                'consume_data' => convertSecondToMinute($data['consumeData']),
                'last_played' => convertSecondToMinute($data['lastPlayed']),
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
        return $this->increseContentlogRound(convertMinuteToSecondFormatted($data['consume_data']), $content->duration, $data['id'], $user);
    }

    public function increseContentlogRound($consume_secounds, $file_secounds, $logID, $user)
    {
        $log = ContentConsumeLog::find($logID);
        if ($consume_secounds > $file_secounds) {
            $log->consume_round = $log->consume_round + 1;
            $log->consume_data = null;
            $log->is_complete = true;
            $log->set = null;
            $log->last_played = null;
            $log->save();

            $content = Content::query()
                ->leftJoinRelationship('lesson.lessonStudyLog', [
                    'lesson' => fn($query) => $query->as('lesson_alias'),
                    'lessonStudyLog' => fn($query) => $query->as('lesson_log_alias')
                        ->on('lesson_log_alias.lesson_id', 'lesson_alias.id'),
                ])
                ->select([
                    'lesson_alias.id as lesson_alias_id',
                    'lesson_log_alias.id as lesson_log_alias_id',
                ])->where('contents.id', $log['content_id'])->first();

            if (isset($content->lesson_log_alias_id)) {
                $lessonLog = LessonStudyLog::find($content->lesson_log_alias_id);
                $lessonLog->study_count = $lessonLog->study_count + 1;
                $lessonLog->last_study_date = now();
                $lessonLog->save();
            } else {
                $this->lessonLogCreate($content->lesson_alias_id, $user);
            }
        }
        return $log;
    }


    public function deactiveContent($data)
    {
        $contentIDs = json_decode($data['deleteContent']);
        $statusId = $this->contentInActiveStatus()->id;

        Content::withoutGlobalScope(ContentScope::class)->whereIn('id', $contentIDs)->update(['status_id' => $statusId]);
    }

    public function contentActiveStatus()
    {
        return Content::GetAllStatuses()->firstWhere('name', ContentStatusEnum::ACTIVE->value);
    }

    public function contentInActiveStatus()
    {
        return Content::GetAllStatuses()->firstWhere('name', ContentStatusEnum::IN_ACTIVE->value);
    }
}
