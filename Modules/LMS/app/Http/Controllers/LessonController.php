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
            return response()->json(['message' => "درس مورد نظر شما ساخته نشد"], 404);
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

        return response()->json(["teacher" => $teacher , "course" => $course , "contentTypes" => $contentTypes]);
    }
}
