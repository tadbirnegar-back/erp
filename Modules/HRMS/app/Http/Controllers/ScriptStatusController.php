<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\HRMS\app\Models\recruitmentScriptStatus;

class ScriptStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rec = RecruitmentScriptStatus::create([
            'recruitment_script_id' => 2709,
            'status_id' => 60,
        ]);
    }
}
