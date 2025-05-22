<?php

namespace Modules\WBM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\AAA\app\Models\User;
use Modules\WBM\app\Http\Traits\DossierWBMTrait;

class DossierWBMController extends Controller
{
    use DossierWBMTrait;
    public function listOfWorksForEngineers(Request $request)
    {
        $data = $request->all();
        $pageNum = $data['pageNum'] ?? 1;
        $perPage = $data['perPage'] ?? 10;
//        $user = Auth::user();
        $user = User::find(2174);
//        return response()->json($user);
        $tasks = $this->TasksOfEngineers($pageNum, $perPage , $user->person_id);
        return response()->json($tasks);

    }
}
