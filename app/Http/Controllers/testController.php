<?php

namespace App\Http\Controllers;
use Modules\LMS\app\Models\Course;


class testController extends Controller
{
    public function run()
    {

        $course = Course::with('allActiveLessons')->find(82);

        return response() -> json($course);


    }
}
