<?php

namespace Modules\HRMS\app\Http\Traits;

use Modules\HRMS\app\Http\Enums\VillageEnum;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\VillageOfc;

trait NewReqScriptTrait
{

    public function LiveSearch(array $data = [])
    {
        $searchTerm = $data['name'] ?? null;

        $query = OrganizationUnit::where('unitable_type', VillageOfc::class)->with('ancestors')
            ->selectRaw('organization_units.name, MATCH(name) AGAINST(?) AS relevance', [$searchTerm])
            ->whereRaw('MATCH(name) AGAINST(?)', [$searchTerm])
            ->orWhere('name', 'LIKE', '%' . $searchTerm . '%')
            ->where('name', VillageEnum::ACTIVE->value)
            ->orderBy('name')
            ->select('id', 'name')
            ->get();

        return $query->flatten();
    }


    public function DropDown()
    {

        return OrganizationUnit::where('unitable_type', DistrictOfc::class)->with('ancestors')->get();
    }


}
