<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\HRMS\app\Models\ExemptionType;
use Modules\HRMS\app\Models\MilitaryServiceStatus;

class MilitaryServicesController extends Controller
{
    public function index()
    {
        $data = MilitaryServiceStatus::get();
        return response()->json($data);
    }

    public function exemptionTypes()
    {
        $data = ExemptionType::get();
        return response()->json($data);
    }
}
