<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\LMS\app\Http\Traits\ChapterTrait;
use Modules\LMS\app\Models\Chapter;

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
            if ($chapter->read_only) {
                return response()->json(['message' => 'شما مجوز پاک کردن این فصل را ندارید'], 403);
            }
            $chapter->load('course', 'lessons');

            $chapter->delete();
            DB::commit();
            return response()->json(["message" => "فصل با موفقیت حفظ شد"]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(["message" => "فصل حذف نشد"]);
        }

    }

}
