<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HRMS\app\Models\Job;
use Modules\HRMS\app\Models\Level;
use Modules\HRMS\app\Models\Position;

class CourseOunitFeatureController extends Controller
{
    public function listing(Request $request)
    {
        $levels = Level::all();
        $positions = Position::all()->where('ounit_cat.value' , $request->ounit_cat_id);
        $jobs = Job::all();

        $data = [
            'levels' => $levels ,
            'positions' => $positions,
            'jobs' => $jobs
        ];

        return response() -> json($data);
    }
}
