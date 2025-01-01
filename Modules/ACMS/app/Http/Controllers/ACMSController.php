<?php

namespace Modules\ACMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;

class ACMSController extends Controller
{

    public function dispatchedCircularsForMyVillage(Request $request)
    {
        $user = Auth::user();
    }
}
