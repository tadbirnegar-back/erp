<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\AAA\app\Models\User;
use Modules\LMS\app\Http\Traits\ContentTrait;

class ContentController extends Controller
{
    use ContentTrait;
    public function setLog(Request $request)
    {
        try {
            \DB::beginTransaction();
            $data = $request->all();
            $user = Auth::user();
            $user->load('student');
            $log = $this->contentLogUpsert($data , $user);

//            $round = $this->calculateRounds($log , $user);
            \DB::commit();
            return response()->json($log);
        }catch (\Exception $exception){
            \DB::rollBack();
            return response() -> json(['error' => $exception->getMessage()], 500);
        }
    }
}
