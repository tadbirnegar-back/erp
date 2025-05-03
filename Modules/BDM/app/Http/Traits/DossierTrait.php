<?php

namespace Modules\BDM\app\Http\Traits;


use Carbon\Carbon;
use Modules\AAA\app\Http\Enums\PermissionTypesEnum;
use Modules\AAA\app\Models\User;
use Modules\BDM\app\Http\Enums\DossierStatusesEnum;
use Modules\BDM\app\Models\BuildingDossier;
use Modules\BDM\app\Models\DossierStatus;
use Modules\BDM\app\Models\PermitStatus;
use Modules\VCM\app\Models\VcmVersions;

trait DossierTrait
{
    use PermitTrait;

    public function makeDossier($ounitID, $ownershipTypeID)
    {
        $dossier = BuildingDossier::create([
            'tracking_code' => null,
            'created_date' => now(),
        ]);

        //Year , Month , Day , OunitID , OwnershipTypeID , dossierID
        $date = convertGregorianToJalali(Carbon::now()->format('Y/m/d'));

        $date = explode('/', $date);


        $trackingCode = convertToEnglishNumbersWithoutZeros($date[0]) . convertToEnglishNumbersWithoutZeros($date[1]) . convertToEnglishNumbersWithoutZeros($date[2]) . $ounitID . $ownershipTypeID . $dossier->id;

        $dossier->tracking_code = $trackingCode;
        $dossier->save();
        return $dossier;
    }

    public function dossiersList($ounits , $perPage , $pageNum)
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
            ->select([
                'bdm_building_dossiers.id as dossier_id',
                'status_dos.name as dossier_status_name',
                'status_dos.class_name as dossier_status_class_name',
                'status_permit.name as permit_status_name',
                'status_permit.class_name as permit_status_class_name',
            ])
//            ->distinct('bdm_building_dossiers.id')
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
