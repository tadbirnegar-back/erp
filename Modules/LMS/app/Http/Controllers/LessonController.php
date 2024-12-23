<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\AAA\app\Models\User;
use Modules\LMS\app\Models\Comment;
use Modules\LMS\app\Models\Lesson;

class LessonController extends Controller
{
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
}
