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
use Modules\LMS\app\Models\Lesson;

class LessonController extends Controller
{
    use ChapterTrait , LessonTrait , ContentTrait;
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
        $data = $request->all();
        //Chapter Part
        $chapter = $data['isNewChapter'] ? $this -> storeChapter($data) : $this -> getChapter($data);
        $data['chapterID'] = $chapter->id;
        //Lesson Part
        $lesson = $this -> storeLesson($data);
        $data['lessonID'] = $lesson->id;
        //LessonFiles
        if(isset($data['lessonFiles']))
        {
            $this->storeLessonFiles($data);
        }
        //Content
        if(isset($data['contents'])){
            $this->storeContent($data);
        }
        return response() -> json($lesson);
    }
}
