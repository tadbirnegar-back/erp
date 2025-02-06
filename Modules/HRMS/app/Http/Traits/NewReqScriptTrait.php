<?php

namespace Modules\HRMS\app\Http\Traits;

use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\VillageOfc;

trait NewReqScriptTrait
{

    public function LiveSearch(array $data = [])
    {
        $searchTerm = $data['name'] ?? null;

        $query = OrganizationUnit::where('unitable_type', VillageOfc::class)
            ->whereRaw('MATCH(name) AGAINST(?)', [$searchTerm])
            ->orWhere('name', 'LIKE', '%' . $searchTerm . '%')
            ->orderBy('name')
            ->with('ancestors')
            ->get();

        return $query->flatten();
    }


    public function DropDown()
    {

        return OrganizationUnit::where('unitable_type', DistrictOfc::class)->with('ancestors')->get();
    }


}
