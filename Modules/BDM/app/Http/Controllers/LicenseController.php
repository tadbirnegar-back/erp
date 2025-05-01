<?php

namespace Modules\BDM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\BDM\app\Http\Enums\BdmOwnershipTypesEnum;
use Modules\BDM\app\Http\Enums\BdmTypesEnum;
use Modules\BDM\app\Http\Enums\TransferTypesEnum;

class LicenseController extends Controller
{
    public function licenseTypesList()
    {
        $list = BdmTypesEnum::listWithIds();
        return response()->json($list);
    }

    public function licenseOwnershipTypesList()
    {
        $list = BdmOwnershipTypesEnum::listWithIds();
        return response()->json($list);
    }

    public function transferTypesList()
    {
        $list = TransferTypesEnum::listWithIds();
        return response()->json($list);
    }
}
