<?php

namespace Modules\BDM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\BDM\app\Http\Enums\BdmOwnershipTypesEnum;
use Modules\BDM\app\Http\Enums\BdmTypesEnum;
use Modules\BDM\app\Http\Enums\TransferTypesEnum;
use Modules\HRMS\app\Models\ExemptionType;
use Modules\HRMS\app\Models\MilitaryService;
use Modules\PersonMS\app\Http\Traits\PersonTrait;
use Modules\PersonMS\app\Models\Natural;
use Modules\PersonMS\app\Models\Person;

class LicenseController extends Controller
{
    use PersonTrait;
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

    public function create(Request $request)
    {
        $data = $request->all();

        $persons = json_decode($data['persons']);
        foreach ($persons as $person) {
            $createdOrUpdatedPerson = $this->personUpdateOrInsert($person);
            $personId = $createdOrUpdatedPerson->id;
            $this -> insertLicenses($personId, $data);
        }




    }
}
