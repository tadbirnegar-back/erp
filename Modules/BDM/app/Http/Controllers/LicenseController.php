<?php

namespace Modules\BDM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\BDM\app\Http\Enums\BdmTypesEnum;

class LicenseController extends Controller
{
    public function licenseTypesList()
    {
        $list = BdmTypesEnum::listWithIds();
        return response()->json($list);
    }
}
