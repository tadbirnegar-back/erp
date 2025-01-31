<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\LMS\app\Models\Privacy;

class PriviciesController extends Controller
{
    public function index()
    {
        return response() -> json(Privacy::all());
    }
}
