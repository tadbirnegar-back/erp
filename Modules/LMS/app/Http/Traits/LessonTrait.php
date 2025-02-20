<?php

namespace Modules\LMS\app\Http\Traits;

use Illuminate\Support\Facades\DB;
use Modules\FileMS\app\Models\File;
use Modules\LMS\app\Http\Enums\LessonStatusEnum;
use Modules\LMS\app\Models\Content;
use Modules\LMS\app\Models\FileLesson;
use Modules\LMS\app\Models\Lesson;
use Modules\LMS\app\Models\LessonStudyLog;
use Modules\LMS\app\Models\StatusLesson;
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

    public function addActiveLessonStatus(Lesson $lesson)
    {
        $statusID = $this->lessonActiveStatus()->id;
        StatusLesson::create([
            'status_id' => $statusID,
            'lesson_id' => $lesson->id,
            'created_date' => now()
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

    public function deleteLessonFiles($lesson, $data)
    {
        $fileIds = json_decode($data['deleteLessonFiles']);

        // Perform a bulk delete using whereIn
        FileLesson::where('lesson_id', $lesson->id)
            ->whereIn('file_id', $fileIds)
            ->delete();
        File::whereIn('id', $fileIds)->delete();
    }

    public function getLessonDatasBasedOnLessonId($lessonID, $user)
    {
        $status = $this->contentActiveStatus();
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
            ->leftJoinRelationship('contents.file', [
                'contents' => fn($join) => $join->on('contents.id', '=', 'contents_alias.id'),
                'file' => fn($join) => $join->as('content_file_alias'),
            ])
            ->leftJoinRelationship('files.file', [
                'file' => fn($join) => $join->as('lesson_files_alias'),
                'files' => fn($join) => $join->as('lesson_pivot_file')
                    ->on('file_lesson.lesson_id', '=', 'lessons.id')
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
                'contents' => fn($join) => $join->on('contents.id', '=', 'contents_alias.id'),
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
                'lesson_files_alias.id as lesson_file_id',
                'lesson_files_alias.slug as lesson_file_slug',
                'lesson_files_alias.size as lesson_file_size',
                'lesson_pivot_file.title as lesson_file_title',
                'comments_alias.text as lesson_comment_text',
                'comments_alias.id as lesson_comment_id',
                'comments_alias.create_date as lesson_comment_create_date',
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
                'content_consume_alias.create_date as content_consume_create_date',
                'content_consume_alias.consume_round as content_consume_consume_round',
            ])
            ->where('lessons.id', $lessonID)
            ->where('contents_alias.status_id', '=', $status->id)
            ->get();
        return ["lessonDetails" => $query];
    }

    public function getLessonDatasForUpdate($lessonID)
    {
        $query = Lesson::query()
            ->leftJoinRelationship('contents.teacher.workForceForJoin.person', [
                'contents' => fn($join) => $join->as('contents_alias')
                    ->withGlobalScopes(),
                'teacher' => fn($join) => $join->as('teacher_alias'),
                'workForceForJoin' => fn($join) => $join->as('workForce_alias')
                    ->on('workForce_alias.workforceable_type', '=', DB::raw("'" . addslashes(Teacher::class) . "'")),
                'person' => fn($join) => $join->as('teacher_person_alias'),
            ])
            ->leftJoinRelationship('contents.contentType', [
                'contents' => fn($join) => $join->on('contents.id', '=', 'contents_alias.id'),
                'contentType' => fn($join) => $join->as('content_type_alias'),
            ])
            ->leftJoinRelationship('files.file', [
                'file' => fn($join) => $join->as('lesson_files_alias'),
                'files' => fn($join) => $join->as('lesson_file_pivot_alias')
                    ->on('file_lesson.lesson_id', '=', 'lessons.id')
            ])
            ->leftJoinRelationship('chapter.course.chapters', [
                'chapter' => fn($join) => $join->as('chapter_alias'),
                'course' => fn($join) => $join->as('course_alias'),
                'chapters' => fn($join) => $join->as('chapters_alias'),
            ])
            ->select([
                'lessons.id as activeLesson',
                'lessons.description as lesson_description',
                'lessons.title as lesson_title',
                'contents_alias.name as content_title',
                'contents_alias.id as content_id',
                'lesson_files_alias.id as lesson_file_id',
                'lesson_files_alias.name as lesson_file_title',
                'lesson_files_alias.size as lesson_file_size',
                'lesson_file_pivot_alias.title as lesson_file_name',
                'teacher_alias.id as teacher_alias_id',
                'teacher_person_alias.display_name as teacher_name',
                'content_type_alias.name as content_type_name',
                'content_type_alias.id as content_type_id',
                'chapter_alias.id as chapter_alias_id',
                'chapter_alias.title as chapter_alias_title',
                'chapters_alias.id as chapters_alias_id',
                'chapters_alias.title as chapters_alias_title',
                'course_alias.id as course_alias_id',
            ])
            ->where('lessons.id', $lessonID)
            ->get();
        return ["lessonDetails" => $query];
    }

    public function getLessonDatasBasedOnContentLog($content_id, $user)
    {
        $content = Content::with('lesson')->find($content_id);
        $lessonID = $content->lesson->id;
        return $this->getLessonDatasBasedOnLessonId($lessonID, $user);
    }


    public function lessonLogCreate($lessonID, $user)
    {
        return LessonStudyLog::create(
            [
                'lesson_id' => $lessonID,
                'student_id' => $user->student->id,
                'study_count' => 1,
                'is_completed' => true,
                'first_study_date' => now(),
                'last_study_date' => now(),
            ]
        );
    }

    public function updateLessonDatas($lesson, $data)
    {
        $lesson->update([
            'description' => $data['lesson_description'],
            'title' => $data['lesson_title'],
            'chapter_id' => $data['chapterID'],
        ]);
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
