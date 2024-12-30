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
        $data = $request->all();
//        $user = Auth::user();
        $user = User::find(2174);
        $user->load('student');
        $log = $this->contentLogUpsert($data , $user);
        $round = $this->calculateRounds($log , $user);
        return response()->json($round);
    }
}
