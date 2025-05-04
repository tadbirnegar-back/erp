<?php

namespace Modules\BDM\app\Http\Traits;


use Carbon\Carbon;
use Modules\AAA\app\Http\Enums\PermissionTypesEnum;
use Modules\AAA\app\Models\User;
use Modules\BDM\app\Http\Enums\DossierStatusesEnum;
use Modules\BDM\app\Models\BuildingDossier;
use Modules\BDM\app\Models\DossierStatus;
use Modules\BDM\app\Models\PermitStatus;
use Modules\PersonMS\app\Http\Enums\PersonLicensesEnums;
use Modules\PersonMS\app\Models\Natural;
use Modules\PersonMS\app\Models\Person;
use Modules\VCM\app\Models\VcmVersions;

trait DossierTrait
{
    use PermitTrait;

    public function makeDossier($ounitID, $ownershipTypeID, $bdmTypeID)
    {
        $dossier = BuildingDossier::create([
            'tracking_code' => null,
            'created_date' => now(),
            'bdm_type_id' => $bdmTypeID,
        ]);

        //Year , Month , Day , OunitID , OwnershipTypeID , dossierID
        $date = convertGregorianToJalali(Carbon::now()->format('Y/m/d'));

        $date = explode('/', $date);


        $trackingCode = convertToEnglishNumbersWithoutZeros($date[0]) . convertToEnglishNumbersWithoutZeros($date[1]) . convertToEnglishNumbersWithoutZeros($date[2]) . $ounitID . $ownershipTypeID . $dossier->id;

        $dossier->tracking_code = $trackingCode;
        $dossier->save();
        return $dossier;
    }

    public function dossiersList($ounits, $perPage, $pageNum, $data)
    {
        $query = BuildingDossier::query()
            ->join('bdm_building_dossier_status', function ($join) {
                $join->on('bdm_building_dossiers.id', '=', 'bdm_building_dossier_status.dossier_id')
                    ->whereRaw('bdm_building_dossier_status.id = (SELECT MAX(id) FROM bdm_building_dossier_status WHERE dossier_id = bdm_building_dossiers.id)');
            })
            ->join('bdm_building_permit_status', function ($join) {
                $join->on('bdm_building_dossiers.id', '=', 'bdm_building_permit_status.dossier_id')
                    ->whereRaw('bdm_building_permit_status.id = (SELECT MAX(id) FROM bdm_building_permit_status WHERE dossier_id = bdm_building_dossiers.id)');
            })
            ->join('statuses as status_dos', 'bdm_building_dossier_status.status_id', '=', 'status_dos.id')
            ->join('statuses as status_permit', 'bdm_building_permit_status.status_id', '=', 'status_permit.id')
            ->join('bdm_owners', function ($join) {
                $join->on('bdm_building_dossiers.id', '=', 'bdm_owners.dossier_id')
                    ->where('is_main_owner', '=', true);
            })
            ->join('persons as main_owner', 'bdm_owners.person_id', '=', 'main_owner.id')
            ->join('naturals', function ($join) {
                $join->on('main_owner.personable_id', '=', 'naturals.id')
                    ->where('main_owner.personable_type', '=', Natural::class);
            })
            ->join('bdm_estates', 'bdm_building_dossiers.id', '=', 'bdm_estates.dossier_id')
            ->join('organization_units as village', 'bdm_estates.ounit_id', '=', 'village.id')
            ->join('organization_units as town', 'village.parent_id', '=', 'town.id')
            ->join('organization_units as district', 'town.parent_id', '=', 'district.id')
            ->select([
                'bdm_building_dossiers.id as dossier_id',
                'status_dos.name as dossier_status_name',
                'status_dos.class_name as dossier_status_class_name',
                'status_permit.name as permit_status_name',
                'status_permit.class_name as permit_status_class_name',
                'bdm_building_dossiers.tracking_code as tracking_code',
                'main_owner.display_name as main_owner_name',
                'bdm_building_dossiers.bdm_type_id as bdm_type_id',
                'village.name as village_name',
                'district.name as district_name',
                'bdm_building_dossiers.created_date as created_date',
                'naturals.mobile as mobile',
            ])
            ->when(isset($data['villageID']), function ($query) use ($data) {
                $query->where('bdm_estates.ounit_id', $data['villageID']);
            })
            ->when(isset($data['districtID']), function ($query) use ($data) {
                $query->where('district.id', $data['districtID']);
            })
            ->when(isset($data['bdmTypeID']), function ($query) use ($data) {
                $query->where('bdm_building_dossiers.bdm_type_id', $data['bdmTypeID']);
            })
            ->when(isset($data['permitStatusID']), function ($query) use ($data) {
                $query->where('status_permit.id', $data['permitStatusID']);
            })
            ->when(isset($data['dossierStatusID']), function ($query) use ($data) {
                $query->where('status_dos.id', $data['dossierStatusID']);
            })
            ->when(isset($data['createdDate']), function ($query) use ($data) {
                $query->whereRaw("DATE(bdm_building_dossiers.created_date) = ?", [
                    date('Y-m-d', strtotime(convertPersianToGregorianBothHaveTimeAndDont($data['createdDate'])))
                ]);
            })
            ->when(isset($data['title']), function ($query) use ($data) {
                $query->where('bdm_building_dossiers.tracking_code', 'like', '%' . $data['title'] . '%')
                    ->orWhere('main_owner.display_name', 'like', '%' . $data['title'] . '%');
            })
            ->whereIn('bdm_estates.ounit_id', $ounits)
            ->paginate($perPage, ['*'], 'page', $pageNum);
        return $query;

    }

    public function attachStatuses($id, $userID)
    {
        DossierStatus::create([
            'dossier_id' => $id,
            'status_id' => $this->waitToDoneStatus()->id,
            'created_date' => now(),
            'creator_id' => $userID,
        ]);

        PermitStatus::create([
            'dossier_id' => $id,
            'status_id' => $this->firstStatus()->id,
            'created_date' => now(),
            'creator_id' => $userID,
        ]);

        PermitStatus::create([
            'dossier_id' => $id,
            'status_id' => $this->secondStatus()->id,
            'created_date' => now(),
            'creator_id' => $userID,
        ]);

        $permitStatuses = $this->preparePermitStatuses($id, $userID);
        PermitStatus::insert($permitStatuses);

    }

    private function preparePermitStatuses($dossierId, $userId)
    {
        $now = now();

        return [
            [
                'dossier_id' => $dossierId,
                'status_id' => $this->firstStatus()->id,
                'created_date' => $now,
                'creator_id' => $userId,
            ],
            [
                'dossier_id' => $dossierId,
                'status_id' => $this->secondStatus()->id,
                'created_date' => $now,
                'creator_id' => $userId,
            ],
        ];
    }

    public function waitToDoneStatus()
    {
        return BuildingDossier::GetAllStatuses()->firstWhere('name', DossierStatusesEnum::WAIT_TO_DONE->value);
    }

    public function doneStatus()
    {
        return BuildingDossier::GetAllStatuses()->firstWhere('name', DossierStatusesEnum::DONE->value);
    }

    public function expiredStatus()
    {
        return BuildingDossier::GetAllStatuses()->firstWhere('name', DossierStatusesEnum::EXPIRED->value);
    }

    public function findCurrentPermitStatusOfDossier($id)
    {
        $query = BuildingDossier::join('bdm_building_permit_status', function ($join) {
            $join->on('bdm_building_dossiers.id', '=', 'bdm_building_permit_status.dossier_id')
                ->whereRaw('bdm_building_permit_status.id = (SELECT MAX(id) FROM bdm_building_permit_status WHERE dossier_id = bdm_building_dossiers.id)');
        })
            ->join('statuses as status_permit', 'bdm_building_permit_status.status_id', '=', 'status_permit.id')
            ->select([
                'bdm_building_dossiers.id as dossier_id',
                'status_permit.name as permit_status_name',
            ])
            ->find($id);

        return $query;
    }

    public function getFooterDatas($id)
    {
        $estateData = $this->getEstates($id);
        $ownersData = $this->getOwners($id);
        return ['estate' => $estateData , 'owners' => $ownersData];
    }

    public function getEstates($dossierID)
    {
        $query = BuildingDossier::join('bdm_estates' , 'bdm_building_dossiers.id' , '=' , 'bdm_estates.dossier_id')
            ->join('organization_units as village', 'bdm_estates.ounit_id', '=', 'village.id')
            ->join('organization_units as town', 'village.parent_id', '=', 'town.id')
            ->join('organization_units as district', 'town.parent_id', '=', 'district.id')
            ->join('organization_units as city', 'district.parent_id', '=', 'city.id')
            ->select([
                'bdm_estates.id as estate_id',
                'bdm_estates.ownership_type_id as ownership_type_id',
                'bdm_estates.area as area',
                'bdm_estates.postal_code as postal_code',
                'bdm_estates.app_id as app_id',
                'bdm_estates.building_number as building_number',
                'bdm_estates.ounit_number as ounit_number',
                'bdm_estates.main as main',
                'bdm_estates.minor as minor',
                'bdm_estates.part as part',
                'city.name as city_name',
                'district.name as district_name',
                'village.name as village_name',
                'bdm_estates.address as address',
            ])
            ->find($dossierID);

        return $query;
    }

    public function getOwners($dossierID)
    {
        $query = BuildingDossier::join('bdm_owners' , 'bdm_building_dossiers.id' , '=' , 'bdm_owners.dossier_id')
            ->join('persons' , 'bdm_owners.person_id' , '=' , 'persons.id')
            ->join('naturals' , function ($join) {
                $join->on('persons.personable_id', '=', 'naturals.id')
                    ->where('persons.personable_type', '=', Natural::class);
            })
            ->join('military_services' ,'persons.id', '=', 'military_services.person_id')
            ->join('military_service_statuses' , 'military_services.military_service_status_id' , '=' , 'military_service_statuses.id')
            ->select([
                'persons.id as person_id',
                'bdm_building_dossiers.id as dossier_id',
                'bdm_owners.is_main_owner as is_main_owner',
                'persons.display_name as display_name',
                'persons.national_code as national_code',
                'naturals.gender_id as gender',
                'naturals.mobile as mobile',
                'naturals.father_name as father_name',
                'naturals.birth_location as birth_location',
                'naturals.birth_date as birth_date',
                'naturals.bc_code as bc_code',
                'naturals.bc_serial as bc_serial',
                'naturals.bc_issue_location as bc_issue_location',
                'naturals.bc_issue_date as bc_issue_date',
                'military_service_statuses.name as military_service_status_name',
            ])
            ->where('bdm_building_dossiers.id' , $dossierID)
            ->get();
        $persons = $query->pluck('person_id')->toArray();
        $licenses = Person::join('person_licenses' , function ($join) {
            $join->on('person_licenses.person_id', '=', 'person.id')
                ->whereIn('person_licenses.license_type_id' , PersonLicensesEnums::listWithIds());
        })
            ->join('files' , 'person_licenses.file_id' , '=' , 'files.id')

        return ;
    }
}
