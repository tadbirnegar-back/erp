<?php

namespace Modules\LMS\app\Http\Traits;

use Illuminate\Support\Facades\DB;
use Modules\LMS\app\Http\Enums\LessonStatusEnum;
use Modules\LMS\app\Models\FileLesson;
use Modules\LMS\app\Models\Lesson;
use Modules\LMS\app\Models\Teacher;

trait LessonTrait
{
    public function storeLesson($data)
    {
        return Lesson::create([
            'chapter_id' => $data['chapterID'],
            'description' => $data['description'],
            'title' => $data['title'],
        ]);
    }

    public function storeLessonFiles($data)
    {
        $insertData = $this->prepareLessonFilesData($data);

        FileLesson::insert($insertData);
    }

    private function prepareLessonFilesData($data)
    {
        // Decode the JSON data
        $lessonFiles = json_decode($data['lessonFiles'], true);

        // Prepare the data
        return array_map(function ($file) use ($data) {
            return [
                'lesson_id' => $data['lessonID'],
                'file_id' => $file['fileID'],
                'title' => $file['fileTitle'],
            ];
        }, $lessonFiles);
    }

    public function getLessonDatasBasedOnLessonId($lessonID, $user)
    {
        $query = Lesson::query()
            ->leftJoinRelationship('contents.teacher.workForceForJoin.person.avatar', [
                'contents' => fn($join) => $join->as('contents_alias'),
                'teacher' => fn($join) => $join->as('teacher_alias'),
                'workForceForJoin' => fn($join) => $join->as('workForce_alias')
                    ->on('workForce_alias.workforceable_type', '=', DB::raw("'" . addslashes(Teacher::class) . "'")),
                'person' => fn($join) => $join->as('teacher_person_alias'),
                'avatar' => fn($join) => $join->as('teacher_avatar_alias'),
            ])
            ->leftJoinRelationship('contents.contentType', [
                'contents' => fn($join) => $join->on('contents.id', '=', 'contents_alias.id'),
                'contentType' => fn($join) => $join->as('content_type_alias'),
            ])
            ->leftJoinRelationship('contents.contentType', [
                'contents' => fn($join) => $join->on('contents.id', '=', 'contents_alias.id'),
                'contentType' => fn($join) => $join->as('content_type_alias'),
            ])
            ->leftJoinRelationship('contents.file', [
                'contents' => fn($join) => $join->on('contents.id', '=', 'contents_alias.id'),
                'file' => fn($join) => $join->as('content_file_alias'),
            ])
            ->leftJoinRelationship('files.file', [
                'file' => fn($join) => $join->as('lesson_files_alias'),
                'files' => fn($join) => $join->on('file_lesson.lesson_id', '=', 'lessons.id')
            ])
            ->leftJoinRelationship('comments.user.person.avatar', [
                'comments' => fn($join) => $join->as('comments_alias')
                    ->on('comments_alias.commentable_id', '=', 'lessons.id')
                    ->on('comments_alias.commentable_type', '=', DB::raw("'" . addslashes(Lesson::class) . "'"))
                    ->on('comments_alias.creator_id', '=', DB::raw($user->id)),
                'avatar' => fn($join) => $join->as('person_avatar_alias'),
                'person' => fn($join) => $join->as('person_alias'),
                'user' => fn($join) => $join->on('users.id', '=', 'comments_alias.creator_id'),
            ])
            ->leftJoinRelationship('contents.consumeLog', [
                'consumeLog' => fn($join) => $join->as('content_consume_alias')
                    ->on('content_id', 'contents_alias.id')
                    ->on('content_consume_alias.student_id', '=', DB::raw($user->student->id)),
            ])
            ->select([
                'lessons.id as activeLesson',
                'lessons.description as lesson_description',
                'lessons.title as lesson_title',
                'contents_alias.name as content_title', // done
                'content_file_alias.slug as content_file_alias', //done
                'contents_alias.id as content_id', // done
                'lesson_files_alias.slug as lesson_file_slug',
                'comments_alias.text as lesson_comment_text',
                'person_alias.display_name as commented_person_name',
                'person_avatar_alias.slug as commented_person_avatar',
                'teacher_alias.id as teacher_alias_id',
                'teacher_person_alias.display_name as teacher_name',
                'teacher_avatar_alias.slug as teacher_avatar',
                'content_type_alias.name as content_type_name',
                'content_consume_alias.consume_data as content_consume_data',
                'content_consume_alias.id as content_consume_id',
                'content_consume_alias.last_played as content_consume_last_played',
                'content_consume_alias.set as content_consume_set',
            ])
            ->where('lessons.id', $lessonID)
            ->get();
        return $query;
    }

    public function lessonActiveStatus()
    {
        return Lesson::GetAllStatuses()->firstWhere('name', LessonStatusEnum::ACTIVE->value);
    }

    public function lessonInActiveStatus()
    {
        return Lesson::GetAllStatuses()->firstWhere('name', LessonStatusEnum::IN_ACTIVE->value);
    }
}
