<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\LMS\app\Http\Traits\ChapterTrait;
use Modules\LMS\app\Models\Chapter;
use Modules\LMS\app\Models\StatusLesson;

class ChapterController extends Controller
{
    use ChapterTrait;

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $chapter = Chapter::find($id);
            if (isset($data['title'])) {
                $chapter->title = $data['title'];
            }

            if (isset($data['description'])) {
                $chapter->description = $data['description'];
            }
            $chapter->save();

            DB::commit();
            return response()->json(['message' => 'ویرایش انجام شد']);

        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['message' => 'ویرایش انجام نشد']);
        }

    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $chapter = Chapter::find($id);
            $chapter->load('course', 'lessons');
            $statusLesson = $this->lessonInActiveStatus()->id;
            $chapter->lessons->each(function ($lesson) use ($statusLesson) {
                StatusLesson::create([
                    'lesson_id' => $lesson->id,
                    'status_id' => $statusLesson,
                ]);
            });

            $chapter->delete();
            DB::commit();
            return response()->json(["message" => "فصل با موفقیت حفظ شد"]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(["message" => "فصل حذف نشد"]);
        }

    }

}
