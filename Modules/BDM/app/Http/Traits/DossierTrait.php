<?php

namespace Modules\BDM\app\Http\Traits;


use Carbon\Carbon;
use Modules\AAA\app\Http\Enums\PermissionTypesEnum;
use Modules\AAA\app\Models\User;
use Modules\BDM\app\Http\Enums\DossierStatusesEnum;
use Modules\BDM\app\Models\BuildingDossier;
use Modules\BDM\app\Models\DossierStatus;
use Modules\BDM\app\Models\PermitStatus;
use Modules\PersonMS\app\Models\Natural;
use Modules\VCM\app\Models\VcmVersions;

trait DossierTrait
{
    use PermitTrait;

    public function makeDossier($ounitID, $ownershipTypeID , $bdmTypeID)
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

    public function dossiersList($ounits , $perPage , $pageNum , $data)
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
            ->join('naturals' , function ($join) {
                $join->on('main_owner.personable_id', '=', 'naturals.id')
                    ->where('main_owner.personable_type', '=', Natural::class);
            })
            ->join('bdm_estates' , 'bdm_building_dossiers.id', '=', 'bdm_estates.dossier_id')
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
            ->when(isset($data['villageID']) , function ($query) use ($data) {
                $query->where('bdm_estates.ounit_id' , $data['villageID']);
            })
            ->when(isset($data['districtID']) , function ($query) use ($data) {
                $query->where('district.id' , $data['districtID']);
            })
            ->when(isset($data['bdmTypeID']) , function ($query) use ($data) {
                $query->where('bdm_building_dossiers.bdm_type_id' , $data['bdmTypeID']);
            })
            ->when(isset($data['permitStatusID']) , function ($query) use ($data) {
                $query->where('status_permit.id' , $data['permitStatusID']);
            })
            ->when(isset($data['dossierStatusID']) , function ($query) use ($data) {
                $query->where('status_dos.id' , $data['dossierStatusID']);
            })
            ->when(isset($data['createdDate']) , function ($query) use ($data) {
                $query->whereRaw("DATE(bdm_building_dossiers.created_date) = ?", [
                    date('Y-m-d', strtotime(convertPersianToGregorianBothHaveTimeAndDont($data['createdDate'])))
                ]);
            })
            ->whereIn('bdm_estates.ounit_id' , $ounits)
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
}
