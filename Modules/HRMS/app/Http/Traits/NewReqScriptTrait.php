<?php

namespace Modules\HRMS\app\Http\Traits;

use Modules\HRMS\app\Models\Position;
use Modules\OUnitMS\app\Models\CityOfc;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\VillageOfc;

trait NewReqScriptTrait
{
    use PositionTrait;

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

        $organizationUnits = OrganizationUnit::where('unitable_type', DistrictOfc::class)
            ->with('ancestors', function ($query) {
                $query->where('unitable_type', '=', CityOfc::class);
            })
            ->get();

        $positions = Position::select(['id', 'name'])
            ->where('status_id', $this->activePositionStatus()->id)
            ->get();

        return response()->json([
            'organization_units' => $organizationUnits,
            'positions' => $positions,
        ]);
    }


}
