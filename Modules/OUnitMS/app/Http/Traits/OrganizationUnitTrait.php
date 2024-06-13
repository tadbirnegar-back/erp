<?php

namespace Modules\OUnitMS\app\Http\Traits;

use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\WorkForce;
use Modules\OUnitMS\app\Models\CityOfc;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\StateOfc;
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

        $state->organizationUnit()->save($organizationUnit);

        $status = $this->activeOrganizationUnitStatus();
        $organizationUnit->statuses()->attach($status->id);

        return $state;
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
        $cityOfc->state_ofc_id = $data['stateOfcID'];
        $cityOfc->save();

        $organizationUnit = new OrganizationUnit();
        $organizationUnit->name = $data['name'];
        $organizationUnit->head_id = $data['headID'] ?? null;

        $cityOfc->organizationUnit()->save($organizationUnit);

//        $status = $this->activeOrganizationUnitStatus();
//        $organizationUnit->statuses()->attach($status->id);

        return $cityOfc;
    }

    public function bakhshdariIndex($searchTerm = null, int $perPage = 10, int $pageNum = 1)
    {
        $districts = OrganizationUnit::where('unitable_type', DistrictOfc::class)
            ->when($searchTerm, function ($query) use ($searchTerm) {
                $query->whereRaw("MATCH (name) AGAINST (? IN BOOLEAN MODE)", [$searchTerm]);
            })
            ->with(['head', 'ancestorsAndSelf'])
            ->paginate($perPage, page: $pageNum);


        return $districts;
    }

    public function bakhshdariStore(array $data)
    {
        $districtOfc = new DistrictOfc();
        $districtOfc->city_ofc_id = $data['cityOfcID'];
        $districtOfc->save();

        $organizationUnit = new OrganizationUnit();
        $organizationUnit->name = $data['name'];
        $organizationUnit->head_id = $data['headID'] ?? null;

        $districtOfc->organizationUnit()->save($organizationUnit);

        $status = $this->activeOrganizationUnitStatus();
        $organizationUnit->statuses()->attach($status->id);

        return $districtOfc;
    }

    public function dehestanIndex($searchTerm = null, int $perPage = 10, int $pageNum = 1)
    {
        $villages = OrganizationUnit::where('unitable_type', VillageOfc::class)
            ->when($searchTerm, function ($query) use ($searchTerm) {
                $query->whereRaw("MATCH (name) AGAINST (? IN BOOLEAN MODE)", [$searchTerm]);
            })
            ->with(['head', 'ancestorsAndSelf'])
            ->paginate($perPage, page: $pageNum);


        return $villages;
    }

    public function dehestanStore(array $data)
    {
        $villageOfc = VillageOfc::create([
            'town_ofc_id' => $data['townOfcID'] ?? null,
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

        $villageOfc->organizationUnit()->save($organizationUnit);

        $status = $this->activeOrganizationUnitStatus();
        $organizationUnit->statuses()->attach($status->id);

        return $villageOfc;
    }

    public function activeOrganizationUnitStatus()
    {
        return OrganizationUnit::GetAllStatuses()->firstWhere('name', 'فعال');
    }

    public function getEmployeesByPersonName(string $searchTerm)
    {
        return WorkForce::where('workforceable_type', Employee::class)
            ->whereHas('person', function ($query) use ($searchTerm) {
                $query->whereRaw('MATCH(display_name) AGAINST(?)', [$searchTerm])
                    ->orWhere('display_name', 'LIKE', '%' . $searchTerm . '%')
                    ->selectRaw('persons.*, MATCH(display_name) AGAINST(?) AS relevance', [$searchTerm])
                    ->orderByDesc('relevance');
            })
            ->with(['person' => function ($query) use ($searchTerm) {
                $query->selectRaw('persons.*, MATCH(display_name) AGAINST(?) AS relevance', [$searchTerm]);
            },'person.user'])
            ->get();


    }
}
