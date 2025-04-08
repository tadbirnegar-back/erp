<?php

namespace Modules\OUnitMS\app\Http\Traits;

use Illuminate\Support\Facades\Cache;
use Modules\OUnitMS\app\Http\Enums\StatusEnum;
use Modules\OUnitMS\app\Models\CityOfc;
use Modules\OUnitMS\app\Models\Department;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\StateOfc;
use Modules\OUnitMS\app\Models\TownOfc;
use Modules\OUnitMS\app\Models\VillageOfc;

trait OrganizationUnitTrait
{
    public function ostandariIndex($searchTerm = null, int $perPage = 10, int $pageNum = 1)
    {
        $states = OrganizationUnit::where('unitable_type', StateOfc::class)
            ->when($searchTerm, function ($query) use ($searchTerm) {
                $query->whereRaw("MATCH (name) AGAINST (? IN BOOLEAN MODE)", [$searchTerm]);
            })
            ->with(['head', 'ancestorsAndSelf'])
            ->paginate($perPage, page: $pageNum);


        return $states;

    }

    public function ostandariStore(array $data)
    {
        $state = new StateOfc();
        $state->save();

        $organizationUnit = new OrganizationUnit();
        $organizationUnit->name = $data['name'];
        $organizationUnit->head_id = $data['headID'] ?? null;
        $status = $this->activeOrganizationUnitStatus();
        $organizationUnit->status_id = $status->id;

        $state->organizationUnit()->save($organizationUnit);


        return $state;
    }


    public function departmentStore(array $data)
    {
        $department = new Department();
        $department->save();

        $organizationUnit = new OrganizationUnit();
        $organizationUnit->head_id = $data['headID'] ?? null;
        $organizationUnit->name = $data['name'] ?? null;
        $status = $this->activeOrganizationUnitStatus();
        $organizationUnit->status_id = $status->id;
        $department->organizationUnit()->save($organizationUnit);

        $status = $this->activeOrganizationUnitStatus();

        return $department;
    }

    public function farmandariIndex($searchTerm = null, int $perPage = 10, int $pageNum = 1)
    {
        $cities = OrganizationUnit::where('unitable_type', CityOfc::class)
            ->when($searchTerm, function ($query) use ($searchTerm) {
                $query->whereRaw("MATCH (name) AGAINST (? IN BOOLEAN MODE)", [$searchTerm]);
            })
            ->with(['head', 'ancestorsAndSelf'])
            ->paginate($perPage, ['*'], 'page', $pageNum);


        return $cities;
    }

    public function farmandariStore(array $data)
    {
        $cityOfc = new CityOfc();
        $ounit = OrganizationUnit::with('unitable')->find($data['stateOfcID']);
        $cityOfc->state_ofc_id = $ounit->unitable->id;
        $cityOfc->save();

        $organizationUnit = new OrganizationUnit();
        $organizationUnit->name = $data['name'];
        $organizationUnit->head_id = $data['headID'] ?? null;
        $organizationUnit->parent_id = $ounit->id;
        $cityOfc->organizationUnit()->save($organizationUnit);

//        $status = $this->activeOrganizationUnitStatus();
//        $organizationUnit->statuses()->attach($status->id);

        return $cityOfc;
    }

    public function bakhshdariIndex($searchTerm = null, $parentID = null, int $perPage = 10, int $pageNum = 1)
    {
        $districts = OrganizationUnit::where('unitable_type', DistrictOfc::class)
            ->when($searchTerm, function ($query) use ($searchTerm) {
                $query->whereRaw("MATCH (name) AGAINST (? IN BOOLEAN MODE)", [$searchTerm]);
            })
            ->when($parentID, function ($query) use ($parentID) {
                $query->where('parent_id', $parentID);
            })
            ->with(['head', 'ancestorsAndSelf'])
            ->paginate($perPage, page: $pageNum);


        return $districts;
    }

    public function bakhshdariStore(array $data)
    {
        $districtOfc = new DistrictOfc();
        $ounit = OrganizationUnit::with('unitable')->find($data['ounitID']);

        $districtOfc->city_ofc_id = $ounit->unitable->id;
        $districtOfc->save();

        $organizationUnit = new OrganizationUnit();
        $organizationUnit->name = $data['name'];
        $organizationUnit->head_id = $data['headID'] ?? null;
        $organizationUnit->parent_id = $ounit->id;
        $status = $this->activeOrganizationUnitStatus();
        $organizationUnit->status_id = $status->id;

        $districtOfc->organizationUnit()->save($organizationUnit);

//        $status = $this->activeOrganizationUnitStatus();
//        $organizationUnit->statuses()->attach($status->id);

        return $districtOfc;
    }

    public function dehestanIndex($searchTerm = null, $parentID = null, int $perPage = 10, int $pageNum = 1)
    {
        $districts = OrganizationUnit::where('unitable_type', TownOfc::class)
            ->when($searchTerm, function ($query) use ($searchTerm) {
                $query->whereRaw("MATCH (name) AGAINST (? IN BOOLEAN MODE)", [$searchTerm]);
            })
            ->when($parentID, function ($query) use ($parentID) {
                $query->where('parent_id', $parentID);
            })
            ->with(['head', 'ancestorsAndSelf'])
            ->paginate($perPage, page: $pageNum);


        return $districts;
    }

    public function departmentIndex($searchTerm = null, $parentID = null, int $perPage = 10, int $pageNum = 1)
    {

        $departemans = OrganizationUnit::where('unitable_type', Department::class)
            ->when($searchTerm, function ($query) use ($searchTerm) {
                $query->whereRaw("MATCH (name) AGAINST (? IN BOOLEAN MODE)", [$searchTerm]);
            })
            ->paginate($perPage, page: $pageNum);
        return $departemans;
    }

    public function dehestanStore(array $data)
    {
        $ounit = OrganizationUnit::with('unitable')->find($data['ounitID']);

        $townOfc = new TownOfc();
        $townOfc->district_ofc_id = $ounit->unitable->id;
        $townOfc->save();

        $organizationUnit = new OrganizationUnit();
        $organizationUnit->name = $data['name'];
        $organizationUnit->head_id = $data['headID'] ?? null;
        $organizationUnit->parent_id = $ounit->id;
        $status = $this->activeOrganizationUnitStatus();
        $organizationUnit->status_id = $status->id;

        $townOfc->organizationUnit()->save($organizationUnit);

//        $status = $this->activeOrganizationUnitStatus();
//        $organizationUnit->statuses()->attach($status->id);

        return $townOfc;
    }

    public function dehyariIndex($searchTerm = null, $parentID = null, int $perPage = 10, int $pageNum = 1)
    {
        $villages = OrganizationUnit::where('unitable_type', VillageOfc::class)
            ->when($searchTerm, function ($query) use ($searchTerm) {
                $query->whereRaw("MATCH (name) AGAINST (? IN BOOLEAN MODE)", [$searchTerm]);
            })
            ->when($parentID, function ($query) use ($parentID) {
                $query->where('parent_id', $parentID);
            })
            ->with(['head', 'ancestorsAndSelf'])
            ->paginate($perPage, page: $pageNum);


        return $villages;
    }

    public function dehyariStore(array $data)
    {
        $ounit = OrganizationUnit::with('unitable')->find($data['ounitID']);

        $villageOfc = VillageOfc::create([
            'town_ofc_id' => $ounit->unitable->id,
            'hierarchy_code' => $data['hierarchyCode'] ?? null,
            'national_uid' => $data['nationalUID'] ?? null,
            'abadi_code' => $data['abadiCode'] ?? null,
            'ofc_code' => $data['ofcCode'] ?? null,
            'population_1395' => $data['population1395'] ?? null,
            'household_1395' => $data['household1395'] ?? null,
            'isTourism' => $data['isTourism'] ?? null,
            'isFarm' => $data['isFarm'] ?? null,
            'isAttached_to_city' => $data['isAttachedToCity'] ?? null,
            'hasLicense' => $data['hasLicense'] ?? null,
            'license_number' => $data['licenseNumber'] ?? null,
            'license_date' => $data['licenseDate'] ?? null,
            'degree' => $data['degree'] ?? null,
        ]);


        $organizationUnit = new OrganizationUnit();
        $organizationUnit->name = $data['name'];
        $organizationUnit->head_id = $data['headID'] ?? null;
        $organizationUnit->parent_id = $ounit->id;
        $status = $this->activeOrganizationUnitStatus();
        $organizationUnit->status_id = $status->id;
        $villageOfc->organizationUnit()->save($organizationUnit);

//        $status = $this->activeOrganizationUnitStatus();
//        $organizationUnit->statuses()->attach($status->id);

        return $villageOfc;
    }

    public function activeOrganizationUnitStatus()
    {
        return OrganizationUnit::GetAllStatuses()->firstWhere('name', 'فعال');
    }


    public function updateCity(array $data, OrganizationUnit $organizationUnit)
    {
        $parentUnit = OrganizationUnit::find($data['ounitID']);
        /**
         * @var CityOfc $cityOfc
         */
        $cityOfc = $organizationUnit->unitable;
        $cityOfc->state_ofc_id = $parentUnit->unitable_id;
        $cityOfc->save();

        $organizationUnit->name = $data['name'];
        $organizationUnit->head_id = $data['headID'] ?? null;
        $organizationUnit->parent_id = $data['ounitID'] ?? null;
        $organizationUnit->save();
        return $organizationUnit;
    }

    public function updateDistrict(array $data, OrganizationUnit $organizationUnit)
    {
        $parentUnit = OrganizationUnit::find($data['ounitID']);
        /**
         * @var DistrictOfc $districtOfc
         */
        $districtOfc = $organizationUnit->unitable;
        $districtOfc->city_ofc_id = $parentUnit->unitable_id;
        $districtOfc->save();

        $organizationUnit->name = $data['name'];
        $organizationUnit->head_id = $data['headID'] ?? null;
        $organizationUnit->parent_id = $data['ounitID'] ?? null;
        $organizationUnit->save();
        return $organizationUnit;
    }

    public function updateTown(array $data, OrganizationUnit $organizationUnit)
    {
        $parentUnit = OrganizationUnit::find($data['ounitID']);
        /**
         * @var TownOfc $townOfc
         */
        $townOfc = $organizationUnit->unitable;
        $townOfc->district_ofc_id = $parentUnit->unitable_id;
        $townOfc->save();

        $organizationUnit->name = $data['name'];
        $organizationUnit->head_id = $data['headID'] ?? null;
        $organizationUnit->parent_id = $data['ounitID'] ?? null;
        $organizationUnit->save();
        return $organizationUnit;
    }

    public function updateVillage(array $data, OrganizationUnit $organizationUnit)
    {
        $parentUnit = OrganizationUnit::find($data['ounitID']);

        $villageOfc = $organizationUnit->unitable;

        $villageOfc->fill([
            'town_ofc_id' => $parentUnit->unitable_id,
            'hierarchy_code' => $data['hierarchyCode'] ?? null,
            'national_uid' => $data['nationalUID'] ?? null,
            'abadi_code' => $data['abadiCode'] ?? null,
            'ofc_code' => $data['ofcCode'] ?? null,
            'population_1395' => $data['population1395'] ?? null,
            'household_1395' => $data['household1395'] ?? null,
            'isTourism' => $data['isTourism'] ?? null,
            'isFarm' => $data['isFarm'] ?? null,
            'isAttached_to_city' => $data['isAttachedToCity'] ?? null,
            'hasLicense' => $data['hasLicense'] ?? null,
            'license_number' => $data['licenseNumber'] ?? null,
            'license_date' => $data['licenseDate'] ?? null,
            'degree' => $data['degree'] ?? null,
        ]);
        $villageOfc->save();

        $organizationUnit->name = $data['name'];
        $organizationUnit->head_id = $data['headID'] ?? null;
        $organizationUnit->parent_id = $data['ounitID'] ?? null;
        $organizationUnit->save();
        return $organizationUnit;
    }

    public function searchOunitByname(string $searchTerm, string $unitable = null)
    {
        $result = OrganizationUnit::when($unitable, function ($query) use ($unitable) {
            $query->where('unitable_type', $unitable);
            $query->with('unitable');
        })
            ->whereRaw(
                "MATCH(name) AGAINST(? IN BOOLEAN MODE)",
                [$searchTerm]
            )
            ->orWhere('name', 'like', $searchTerm)
            ->orWhere('name', 'like', "{$searchTerm}%")
            ->with(['positions.levels', 'person', 'ancestors', 'village'])->get();

        $result->each(function ($item) {
            $item->unitable->setAttribute('abadiCode', $item->abadi_code);
        });

        return $result;
    }

    public function getActiveStatuses()
    {
        return Cache::rememberForever('ounit_active_statuses', function () {
            return OrganizationUnit::GetAllStatuses()
                ->firstWhere('name', '=', StatusEnum::Active->value);
        });
    }

    public function getInactiveStatuses()
    {
        return Cache::rememberForever('ounit_inactive_statuses', function () {
            return OrganizationUnit::GetAllStatuses()
                ->firstWhere('name', '=', StatusEnum::Inactive->value);
        });
    }

    public function softDeletingOunits(OrganizationUnit $ounit)
    {

        $status = $this->getInactiveStatuses();
        $ounit->status_id = $status->id;
        $ounit->save();
    }
}
