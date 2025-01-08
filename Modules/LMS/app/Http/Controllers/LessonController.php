<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\AAA\app\Models\User;
use Modules\LMS\app\Http\Traits\ChapterTrait;
use Modules\LMS\app\Http\Traits\ContentTrait;
use Modules\LMS\app\Http\Traits\LessonTrait;
use Modules\LMS\app\Models\Comment;
use Modules\LMS\app\Models\ContentType;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\Lesson;
use Modules\LMS\app\Models\Teacher;
use Modules\LMS\app\Resources\LessonDataForupdateResource;
use Modules\LMS\app\Resources\LessonDatasWithLessonIDResource;

class LessonController extends Controller
{
    use ChapterTrait, LessonTrait, ContentTrait;

    public function storeComment(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();

            $user = Auth::user();
            Comment::create([
                'text' => $data['text'],
                'commentable_id' => $data['lesson_id'],
                'creator_id' => $user->id,
                'commentable_type' => Lesson::class,
                'create_date' => now()
            ]);
            DB::commit();
            return response()->json(['message' => "نظر شما ذخیره شد"], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['message' => "نظر شما ذخیره نشد"], 400);
        }
    }

    public function addLesson(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            //Chapter Part
            $chapter = $data['isNewChapter'] ? $this->storeChapter($data) : $this->getChapter($data);
            $data['chapterID'] = $chapter->id;
            //Lesson Part
            $lesson = $this->storeLesson($data);
            //Status Lesson
            $this->addActiveLessonStatus($lesson);
            $data['lessonID'] = $lesson->id;
            //LessonFiles
            if (isset($data['lessonFiles'])) {
                $this->storeLessonFiles($data);
            }
            //Content
            if (isset($data['contents'])) {
                $this->storeContent($data);
            }
            DB::commit();
            return response()->json(['message' => "درس مورد نظر شما با موفقیت ساخته شد"], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['message' => $exception->getMessage()], 404);
        }
    }

    public function addLessonRequirements($id)
    {
        $course = Course::joinRelationship('chapters', function ($join) {
            $join->as('chapter_alias');
        })
            ->where('courses.id', $id)
            ->select('chapter_alias.id as chapter_id', 'chapter_alias.title as chapter_title')
            ->get();

        $teacher = Teacher::with(['person' => function ($query) {
            $query->select('display_name');
        }])->get();

        $contentTypes = ContentType::all();
        if ($course->isEmpty()) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        return response()->json(["teacher" => $teacher, "course" => $course, "contentTypes" => $contentTypes]);
    }

    public function sendLessonDatas(Request $request)
    {
        $user = Auth::user();
        $user->load('student');
        $data = $request->all();

        if (isset($data['contentID'])) {
            $log = $this->contentLogUpsert($data, $user);
            $this->calculateRounds($log, $user);
            $lessonDatas = $this->getLessonDatasBasedOnContentLog($data['contentID'], $user);
        } else {
            $lessonDatas = $this->getLessonDatasBasedOnLessonId($data['lessonID'], $user);
        }
        $response = new LessonDatasWithLessonIDResource($lessonDatas);
        return response()->json($response);
    }

    public function show($id)
    {
        $lesson = Lesson::find($id);
        if(empty($lesson))
        {
            return response()->json(['message' => 'Lesson not found'], 403);
        }
        $lessonData = $this->getLessonDatasForUpdate($id);
        $response = new LessonDataForupdateResource($lessonData);

        $teacher = Teacher::with(['person' => function ($query) {
            $query->select('display_name');
        }])->get();

        $contentTypes = ContentType::all();

        return response()->json(["mainData" => $response ,"teacher" => $teacher, "contentTypes" => $contentTypes]);
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $lesson = Lesson::find($id);
            $chapter = $data['isNewChapter'] ? $this->storeChapter($data) : $this->getChapter($data);
            $data['chapterID'] = $chapter->id;
            $this->updateLessonDatas($lesson, $data);

            if (isset($data['deleteLessonFiles'])) {
                $this->deleteLessonFiles($lesson, $data);
            }

            if (isset($data['deleteContent'])) {
                $this->deactiveContent($data);
            }

            $data['lessonID'] = $lesson->id;
            //LessonFiles
            if (isset($data['lessonFiles'])) {
                $this->storeLessonFiles($data);
            }
            //Content
            if (!is_null($data['contents'])) {
                $this->storeContent($data);
            }
            DB::commit();
            return response()->json(['message' => "دوره با موفقیت ویرایش شد"]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }
}
