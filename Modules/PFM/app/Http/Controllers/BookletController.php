<?php

namespace Modules\PFM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\AAA\app\Models\User;
use Modules\PFM\app\Http\Traits\BookletTrait;
use Modules\PFM\app\Resources\ListOfBookletsResource;
use Modules\PFM\app\Resources\ShowBookletResource;

class BookletController extends Controller
{
    use BookletTrait;

    public function index(Request $request)
    {
        $data = $request->all();

        $pageNum = $data['pageNum'] ?? 1;
        $perPage = $data['perPage'] ?? 10;

//        $user = Auth::user();
        $user = User::find(2174);
        $data = $this->listOfBooklets($data, $user, $pageNum, $perPage);

        return ListOfBookletsResource::collection($data);
    }

    public function show($id)
    {
        $user = User::find(2174);
        $query = $this->showBooklet($id, $user);
        return response() -> json($query);
        if ($query['status'] == 200) {
            return new ShowBookletResource($query);
        } else {
            return response()->json(['message' => $query['message']], $query['status']);
        }
    }
}
