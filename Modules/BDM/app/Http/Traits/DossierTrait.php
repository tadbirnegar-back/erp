<?php

namespace Modules\BDM\app\Http\Traits;


use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\AAA\app\Http\Enums\PermissionTypesEnum;
use Modules\AAA\app\Models\User;
use Modules\ACMS\app\Models\FiscalYear;
use Modules\BDM\app\Http\Enums\BdmOwnershipTypesEnum;
use Modules\BDM\app\Http\Enums\BdmTypesEnum;
use Modules\BDM\app\Http\Enums\DocumentsNameEnum;
use Modules\BDM\app\Http\Enums\DossierStatusesEnum;
use Modules\BDM\app\Http\Enums\EngineersTypeEnum;
use Modules\BDM\app\Http\Enums\FloorNumbersEnum;
use Modules\BDM\app\Http\Enums\GeographicalCordinatesTypesEnum;
use Modules\BDM\app\Http\Enums\PermitStatusesEnum;
use Modules\BDM\app\Models\Building;
use Modules\BDM\app\Models\BuildingDossier;
use Modules\BDM\app\Models\DossierStatus;
use Modules\BDM\app\Models\Estate;
use Modules\BDM\app\Models\Form;
use Modules\BDM\app\Models\LicenseDocument;
use Modules\BDM\app\Models\Obligation;
use Modules\BDM\app\Models\Parking;
use Modules\BDM\app\Models\Partitioning;
use Modules\BDM\app\Models\Pavilion;
use Modules\BDM\app\Models\PermitStatus;
use Modules\BDM\app\Models\Plan;
use Modules\BDM\app\Models\Pool;
use Modules\BDM\app\Models\Structure;
use Modules\ODOC\app\Http\Enums\OdocDocumentComponentsTypeEnum;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\PersonMS\app\Http\Enums\PersonLicensesEnums;
use Modules\PersonMS\app\Models\Natural;
use Modules\PersonMS\app\Models\Person;
use Modules\PFM\app\Http\Enums\ApplicationsCoefficientEnum;
use Modules\PFM\app\Http\Enums\LeviesListEnum;
use Modules\PFM\app\Http\Traits\BillsTrait;
use Modules\PFM\app\Models\Application;
use Modules\PFM\app\Models\Bill;
use Modules\PFM\app\Models\BillItemProperty;
use Modules\PFM\app\Models\BillTariff;
use Modules\PFM\app\Models\Booklet;
use Modules\PFM\app\Models\Levy;
use Modules\PFM\app\Models\LevyBill;
use Modules\PFM\app\Models\LevyItem;
use Modules\PFM\app\Models\PfmCirculars;
use Modules\PFM\app\Models\Tarrifs;
use Modules\PFM\Services\PaymentService;
use Modules\StatusMS\app\Models\Status;
use Modules\VCM\app\Models\VcmVersions;

trait DossierTrait
{
    use PermitTrait, BillsTrait;

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
            ->whereNot('status_dos.name', DossierStatusesEnum::ARCHIVE->value)
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

    public function archiveStatus()
    {
        return BuildingDossier::GetAllStatuses()->firstWhere('name', DossierStatusesEnum::ARCHIVE->value);
    }


    public function getTimelineData($dossierID)
    {
        $status = $this->findCurrentPermitStatusOfDossier($dossierID);
        $currentStatusName = $status->permit_status_name;
        if ($currentStatusName == PermitStatusesEnum::failed->value) {
            $nextStatusData = [
                'permit_status_name' => PermitStatusesEnum::ninth->value,
                'permit_status_class_name' => 'primary',
            ];
            $nextStatusForButtons = PermitStatusesEnum::eighth->value;

            $doc = LicenseDocument::where('dossier_id', $dossierID)->where('documentable_type', Plan::class)
                ->where('name', DocumentsNameEnum::PLAN->value)
                ->first();
            if ($doc) {
                $buttons = ['submit', 'upload'];
            } else {
                $buttons = ['notActiveSubmit', 'upload'];
            }

            $percent = $this->getPercentOfDossier($nextStatusForButtons);
            $uploadedFiles = $this->getUploadedFiles($nextStatusData['permit_status_name'], $dossierID);
            $doneStatuses = $this->doneStatuses($dossierID);
            return ['status' => $nextStatusData, 'percent' => $percent, 'doneStatuses' => $doneStatuses, 'uploadedFiles' => $uploadedFiles, "buttons" => $buttons, 'isAllDone' => false];
        } elseif ($currentStatusName == PermitStatusesEnum::rejectObligations->value) {
            $nextStatusData = [
                'permit_status_name' => PermitStatusesEnum::eleventh->value,
                'permit_status_class_name' => 'primary',
            ];
            $nextStatusForButtons = PermitStatusesEnum::tenth->value;

            $buttons = $this->getButtons()[$nextStatusForButtons];

            $percent = $this->getPercentOfDossier($nextStatusForButtons);
            $uploadedFiles = $this->getUploadedFiles($nextStatusForButtons, $dossierID);
            $doneStatuses = $this->doneStatuses($dossierID);
            return ['status' => $nextStatusData, 'percent' => $percent, 'doneStatuses' => $doneStatuses, 'uploadedFiles' => $uploadedFiles, "buttons" => $buttons, 'isAllDone' => false];
        } elseif ($currentStatusName == PermitStatusesEnum::twentyfirst->value) {
            $nextStatusData = [
                'permit_status_name' => PermitStatusesEnum::twentyfirst->value,
                'permit_status_class_name' => 'success',
            ];
            $nextStatusForButtons = PermitStatusesEnum::twentieth->value;

            $buttons = $this->getButtons()[$nextStatusForButtons];

            $percent = 100;
            $uploadedFiles = $this->getUploadedFiles($nextStatusForButtons, $dossierID);
            $doneStatuses = $this->doneStatuses($dossierID);
            return ['status' => $nextStatusData, 'percent' => $percent, 'doneStatuses' => $doneStatuses, 'uploadedFiles' => $uploadedFiles, 'buttons' => $buttons, 'isAllDone' => true];
        } else {
            $currentEnum = PermitStatusesEnum::tryFrom($currentStatusName);
            if ($currentEnum) {
                $currentId = $currentEnum->id();
                $nextEnum = array_filter(PermitStatusesEnum::cases(), fn($case) => $case->id() === $currentId + 1);
                $nextEnum = reset($nextEnum);
            }
            $nextStatusData = [
                'permit_status_name' => $nextEnum->value,
                'permit_status_class_name' => 'primary',
            ];
            $buttons = $this->getButtons()[$status->permit_status_name];

            if ($nextEnum->value == PermitStatusesEnum::fifth->value) {

                $esatate = Estate::where('dossier_id', $dossierID)->first();

                if ($esatate->propery_sketch_file_id != null) {
                    $buttons = ['submit', 'completeEstate'];
                } else {
                    $buttons = ['notActiveSubmit', 'completeEstate'];
                }
            }

            if ($nextEnum->value == PermitStatusesEnum::sixth->value) {
                $doc = LicenseDocument::where('dossier_id', $dossierID)->where('documentable_type', Form::class)
                    ->where('dossier_id', $dossierID)
                    ->where('name', DocumentsNameEnum::MALEKIYATE_ZAMIN->value)
                    ->first();
                if ($doc) {
                    $buttons = ['submit', 'upload'];
                } else {
                    $buttons = ['notActiveSubmit', 'upload'];
                }
            }

            if ($nextEnum->value == PermitStatusesEnum::twentyfirst->value) {
                $buttons = [];
            }

            if ($nextEnum->value == PermitStatusesEnum::ninth->value) {
                $doc = LicenseDocument::where('dossier_id', $dossierID)->where('documentable_type', Plan::class)
                    ->where('name', DocumentsNameEnum::PLAN->value)
                    ->first();
                if ($doc) {
                    $buttons = ['submit', 'upload'];
                } else {
                    $buttons = ['notActiveSubmit', 'upload'];
                }
            }

            $percent = $this->getPercentOfDossier($status->permit_status_name);
            $uploadedFiles = $this->getUploadedFiles($status->permit_status_name, $dossierID);
            $doneStatuses = $this->doneStatuses($dossierID);
            return ['status' => $nextStatusData, 'percent' => $percent, 'doneStatuses' => $doneStatuses, 'uploadedFiles' => $uploadedFiles, 'buttons' => $buttons, 'isAllDone' => false];
        }


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
                'status_permit.class_name as permit_status_class_name',
                'bdm_building_permit_status.created_date as permit_status_created_date',
            ])
            ->find($id);

        return $query;
    }

    public function doneStatuses($dossierID)
    {
        $query = BuildingDossier::join('bdm_building_permit_status', function ($join) {
            $join->on('bdm_building_dossiers.id', '=', 'bdm_building_permit_status.dossier_id');
        })
            ->join('statuses as status_permit', 'bdm_building_permit_status.status_id', '=', 'status_permit.id')
            ->select([
                'bdm_building_permit_status.id as permit_status_id',
                'bdm_building_dossiers.id as dossier_id',
                'status_permit.name as permit_status_name',
                'bdm_building_permit_status.created_date as permit_status_created_date',
            ])
            ->where('bdm_building_dossiers.id', $dossierID)
            ->get();
        return $query;
    }

    public function getFooterDatas($id)
    {
        $estateData = $this->getEstates($id);
        $ownersData = $this->getOwners($id);
        $lawyers = $this->getLawyers($id);
        $structures = $this->getStructures($id);
        $engineers = $this->getEngineers($id);
        $payments = $this->getPayments($id);
        $plan = $this->getPlan($id);
        return ['estate' => $estateData, 'owners' => $ownersData, 'lawyers' => $lawyers, "structures" => $structures, 'engineers' => $engineers, 'payments' => $payments, 'plan' => $plan];
    }

    public function getEstates($dossierID)
    {
        $query = BuildingDossier::join('bdm_estates', 'bdm_building_dossiers.id', '=', 'bdm_estates.dossier_id')
            ->join('organization_units as village', 'bdm_estates.ounit_id', '=', 'village.id')
            ->join('organization_units as town', 'village.parent_id', '=', 'town.id')
            ->join('organization_units as district', 'town.parent_id', '=', 'district.id')
            ->join('organization_units as city', 'district.parent_id', '=', 'city.id')
            ->join('bdm_estate_app_suggests', 'bdm_estates.id', '=', 'bdm_estate_app_suggests.estate_id')
            ->join('pfm_prop_applications', 'bdm_estate_app_suggests.app_id', '=', 'pfm_prop_applications.id')
            ->select([
                'bdm_estates.id as estate_id',
                'bdm_estates.ownership_type_id as ownership_type_id',
                'bdm_estates.area as area',
                'bdm_estates.postal_code as postal_code',
                'bdm_estates.building_number as building_number',
                'bdm_estates.ounit_number as ounit_number',
                'bdm_estates.main as main',
                'bdm_estates.minor as minor',
                'bdm_estates.part as part',
                'pfm_prop_applications.name',
                'city.name as city_name',
                'district.name as district_name',
                'village.name as village_name',
                'bdm_estates.address as address',
            ])
            ->find($dossierID);

        $query->ownership_type_name = BdmOwnershipTypesEnum::getNameById($query->ownership_type_id);

        return $query;
    }

    public function getOwners($dossierID)
    {
        $query = BuildingDossier::join('bdm_owners', 'bdm_building_dossiers.id', '=', 'bdm_owners.dossier_id')
            ->join('persons', 'bdm_owners.person_id', '=', 'persons.id')
            ->join('naturals', function ($join) {
                $join->on('persons.personable_id', '=', 'naturals.id')
                    ->where('persons.personable_type', '=', Natural::class);
            })
            ->join('military_services', 'persons.id', '=', 'military_services.person_id')
            ->join('military_service_statuses', 'military_services.military_service_status_id', '=', 'military_service_statuses.id')
            ->join('files', 'persons.signature_file_id', '=', 'files.id')
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
                'files.slug as signature_file_slug',
            ])
            ->where('bdm_building_dossiers.id', $dossierID)
            ->get();
        $query->map(function ($item) {
            $item->type = 'شریک';
            $item->signature_file_slug = url($item->signature_file_slug);
        });
        $persons = $query->pluck('person_id')->toArray();
        $licenses = Person::join('person_licenses', function ($join) {
            $join->on('person_licenses.person_id', '=', 'persons.id')
                ->whereIn('person_licenses.license_type', [PersonLicensesEnums::NATIONAL_ID_CARD->value, PersonLicensesEnums::BIRTH_CERTIFICATE->value]);
        })
            ->join('files', 'person_licenses.file_id', '=', 'files.id')
            ->select([
                'persons.id as person_id',
                'person_licenses.license_type as license_type',
                'files.slug as file_slug',
            ])->whereIn('persons.id', $persons)
            ->get();
        $finalResult = $query->map(function ($item) use ($licenses) {
            $item->licenses = $licenses->filter(function ($license) use ($item) {
                return $license->person_id == $item->person_id;
            })->map(function ($license) {
                return [
                    'license_type' => $license->license_type,
                    'file_slug' => url($license->file_slug),
                ];
            })->values()->toArray();

            return $item;
        });

        return $finalResult;
    }

    public function getLawyers($dossierID)
    {
        $query = BuildingDossier::join('bdm_dossier_lawyers', 'bdm_dossier_lawyers.dossier_id', '=', 'bdm_building_dossiers.id')
            ->join('bdm_lawyers', 'bdm_dossier_lawyers.lawyer_id', '=', 'bdm_lawyers.id')
            ->join('persons', 'bdm_lawyers.person_id', '=', 'persons.id')
            ->join('naturals', function ($join) {
                $join->on('persons.personable_id', '=', 'naturals.id')
                    ->where('persons.personable_type', '=', Natural::class);
            })
            ->join('files', 'persons.signature_file_id', '=', 'files.id')
            ->join('military_services', 'persons.id', '=', 'military_services.person_id')
            ->join('military_service_statuses', 'military_services.military_service_status_id', '=', 'military_service_statuses.id')
            ->select([
                'persons.id as person_id',
                'bdm_building_dossiers.id as dossier_id',
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
                'files.slug as signature_file_slug',
            ])
            ->where('bdm_building_dossiers.id', $dossierID)
            ->get();

        $query->map(function ($item) {
            $item->type = 'وکیل';
            $item->signature_file_slug = url($item->signature_file_slug);
        });
        $persons = $query->pluck('person_id')->toArray();
        $licenses = Person::join('person_licenses', function ($join) {
            $join->on('person_licenses.person_id', '=', 'persons.id')
                ->whereIn('person_licenses.license_type', [PersonLicensesEnums::NATIONAL_ID_CARD->value, PersonLicensesEnums::BIRTH_CERTIFICATE->value]);
        })
            ->join('files', 'person_licenses.file_id', '=', 'files.id')
            ->select([
                'persons.id as person_id',
                'person_licenses.license_type as license_type',
                'files.slug as file_slug',
            ])->whereIn('persons.id', $persons)
            ->get();

        $finalResult = $query->map(function ($item) use ($licenses) {
            $item->licenses = $licenses->filter(function ($license) use ($item) {
                return $license->person_id == $item->person_id;
            })->map(function ($license) {
                return [
                    'license_type' => $license->license_type,
                    'file_slug' => url($license->file_slug),
                ];
            })->values()->toArray();

            return $item;
        });

        return $finalResult;
    }

    public function getPercentOfDossier($permitStatusName)
    {
        foreach (PermitStatusesEnum::cases() as $case) {
            if ($case->value == $permitStatusName) {
                $stepNumber = $case->id();
                $totalSteps = count(PermitStatusesEnum::cases()) - 2;
                return ($stepNumber / $totalSteps) * 100;
            }
        }

        return null;
    }

    public function upgradeOneLevel($id)
    {
//        $user = \Auth::user();
        $user = User::find(2174);
        $currentStatus = $this->findCurrentPermitStatusOfDossier($id);

        $currentStatusName = $currentStatus->permit_status_name;

        $this->fullFillOdocDocument($id, $currentStatusName);


        if ($currentStatusName == PermitStatusesEnum::failed->value) {
            $status = $this->ninthStatus();
            PermitStatus::create([
                'dossier_id' => $id,
                'status_id' => $status->id,
                'created_date' => now(),
                'creator_id' => $user->id,
            ]);
            return $status->name;
        } elseif ($currentStatusName == PermitStatusesEnum::rejectObligations->value) {
            $status = $this->eleventhStatus();
            PermitStatus::create([
                'dossier_id' => $id,
                'status_id' => $status->id,
                'created_date' => now(),
                'creator_id' => $user->id,
            ]);
            return $status->name;
        } else {
            $currentEnum = PermitStatusesEnum::tryFrom($currentStatusName);
            if ($currentEnum) {
                $currentId = $currentEnum->id();
                $nextEnum = array_filter(PermitStatusesEnum::cases(), fn($case) => $case->id() === $currentId + 1);
                $nextEnum = reset($nextEnum);

            }
            $nextStatus = Status::where('name', $nextEnum)->where('model', PermitStatus::class)->first();


            PermitStatus::create([
                'dossier_id' => $id,
                'status_id' => $nextStatus->id,
                'created_date' => now(),
                'creator_id' => $user->id,
            ]);


            if ($currentStatusName == PermitStatusesEnum::twentieth->value) {
                DossierStatus::create([
                    'dossier_id' => $id,
                    'status_id' => $this->doneStatus()->id,
                    'created_date' => now(),
                    'creator_id' => $user->id,
                ]);
            }

            return $nextStatus->name;
        }


    }

    public function fullFillOdocDocument($id, $currentStatusName)
    {
        if ($currentStatusName == permitStatusesEnum::first->value) {
            $dossier = BuildingDossier::join('bdm_estates', 'bdm_building_dossiers.id', '=', 'bdm_estates.dossier_id')
                ->join('organization_units as village', 'bdm_estates.ounit_id', '=', 'village.id')
                ->join('organization_units as town', 'village.parent_id', '=', 'town.id')
                ->join('organization_units as district', 'town.parent_id', '=', 'district.id')
                ->join('organization_units as city', 'district.parent_id', '=', 'city.id')
                ->join('bdm_owners', function ($join) {
                    $join->on('bdm_owners.dossier_id', '=', 'bdm_building_dossiers.id')
                        ->where('is_main_owner', '=', true);
                })
                ->join('persons', 'bdm_owners.person_id', '=', 'persons.id')
                ->join('naturals', function ($join) {
                    $join->on('persons.personable_id', '=', 'naturals.id')
                        ->where('persons.personable_type', '=', Natural::class);
                })
                ->join('bdm_geographic_cordinates', function ($join) {
                    $join->on('bdm_geographic_cordinates.dossier_id', '=', 'bdm_building_dossiers.id')
                        ->where('bdm_geographic_cordinates.type_id', '=', GeographicalCordinatesTypesEnum::SUBMITTED->id());
                })
                ->select([
                    'persons.display_name',
                    'persons.national_code',
                    'bdm_building_dossiers.id',
                    'bdm_building_dossiers.tracking_code',
                    'bdm_building_dossiers.created_date',
                    'bdm_building_dossiers.bdm_type_id',
                    'naturals.father_name',
                    'naturals.gender_id',
                    'naturals.birth_location',
                    'naturals.bc_code',
                    'city.name as city_name',
                    'district.name as district_name',
                    'village.id as village_id',
                    'village.name as village_name',
                    'bdm_estates.building_number',
                    'bdm_estates.area',
                    'bdm_estates.address',
                    'bdm_geographic_cordinates.east',
                    'bdm_geographic_cordinates.north',
                    'bdm_geographic_cordinates.west',
                    'bdm_geographic_cordinates.south',
                    'naturals.bc_issue_location as bc_issue_location',
                ])
                ->find($id);
            $dossier->bdm_type_name = BdmTypesEnum::getNameById($dossier->bdm_type_id);
            $dossier->created_date = convertGregorianToJalali($dossier->created_date);
            $dossier->gender_name = $dossier->gender_id == 1 ? 'مرد' : 'زن';
            $dossier->ounit_id = $dossier->village_id;
            $approver = OrganizationUnit::find($dossier->village_id)->head_id;
            $user = User::find($approver);
            $dossier->approvers = [
                [
                    'person_id' => $user->person_id,
                    'status_id' => $this->PendingApproversStatus()->id,
                    'signed_date' => Carbon::now(),
                    'token' => null,
                    'signature_id' => null,
                    'document_id' => null,
                ]
            ];

            $dossier->component_to_render = OdocDocumentComponentsTypeEnum::BONYADE_MASKAN->value;
            $dossier->model_id = $id;
            $dossier->version = '1';
            $dossier->status_id = $this->PendingOdocDocumentStatus()->id;
            $dossier->model = BuildingDossier::class;
            $dossier->title = 'نامه استعلام از بنیاد مسکن';
            $this->storeOdocDocument($dossier, $user->id);
            return $dossier;
        }

        if($currentStatusName == permitStatusesEnum::fifth->value){
            $formNumber2 = BuildingDossier::join('bdm_estates', 'bdm_building_dossiers.id', '=', 'bdm_estates.dossier_id')
                ->join('organization_units as village', 'bdm_estates.ounit_id', '=', 'village.id')
                ->join('organization_units as town', 'village.parent_id', '=', 'town.id')
                ->join('organization_units as district', 'town.parent_id', '=', 'district.id')
                ->join('organization_units as city', 'district.parent_id', '=', 'city.id')
                ->join('bdm_owners', function ($join) {
                    $join->on('bdm_owners.dossier_id', '=', 'bdm_building_dossiers.id')
                        ->where('is_main_owner', '=', true);
                })
                ->join('persons', 'bdm_owners.person_id', '=', 'persons.id')
                ->join('naturals', function ($join) {
                    $join->on('persons.personable_id', '=', 'naturals.id')
                        ->where('persons.personable_type', '=', Natural::class);
                })
                ->join('bdm_geographic_cordinates', function ($join) {
                    $join->on('bdm_geographic_cordinates.dossier_id', '=', 'bdm_building_dossiers.id')
                        ->where('bdm_geographic_cordinates.type_id', '=', GeographicalCordinatesTypesEnum::SUBMITTED->id());
                })
                ->select([
                    'persons.display_name',
                    'persons.national_code',
                    'bdm_building_dossiers.id',
                    'bdm_building_dossiers.tracking_code',
                    'bdm_building_dossiers.created_date',
                    'bdm_building_dossiers.bdm_type_id',
                    'naturals.father_name',
                    'naturals.gender_id',
                    'naturals.birth_location',
                    'naturals.bc_code',
                    'city.name as city_name',
                    'district.name as district_name',
                    'village.id as village_id',
                    'village.name as village_name',
                    'bdm_estates.building_number',
                    'bdm_estates.area',
                    'bdm_estates.address',
                    'bdm_geographic_cordinates.east',
                    'bdm_geographic_cordinates.north',
                    'bdm_geographic_cordinates.west',
                    'bdm_geographic_cordinates.south',
                    'naturals.bc_issue_location as bc_issue_location',
                ])
                ->find($id);
            $formNumber2->bdm_type_name = BdmTypesEnum::getNameById($formNumber2->bdm_type_id);
            $formNumber2->created_date = convertGregorianToJalali($formNumber2->created_date);
            $formNumber2->gender_name = $formNumber2->gender_id == 1 ? 'مرد' : 'زن';
            $formNumber2->ounit_id = $formNumber2->village_id;
            $approver = OrganizationUnit::find($formNumber2->village_id)->head_id;
            $user = User::find($approver);
            $formNumber2->approvers = [
                [
                    'person_id' => $user->person_id,
                    'status_id' => $this->PendingApproversStatus()->id,
                    'signed_date' => Carbon::now(),
                    'token' => null,
                    'signature_id' => null,
                    'document_id' => null,
                ]
            ];

            $formNumber2->component_to_render = OdocDocumentComponentsTypeEnum::BONYADE_MASKAN->value;
            $formNumber2->model_id = $id;
            $formNumber2->version = '1';
            $formNumber2->status_id = $this->PendingOdocDocumentStatus()->id;
            $formNumber2->model = BuildingDossier::class;
            $formNumber2->title = 'نامه استعلام از بنیاد مسکن';
            $this->storeOdocDocument($formNumber2, $user->id);
            return $formNumber2;
        }
    }

    public function uploadFilesByStatus($id, $fileID, $fileName, $user)
    {
        $lastStatus = $this->findCurrentPermitStatusOfDossier($id);
        $lastStatusName = $lastStatus->permit_status_name;

        $uploadFormStatuses = [
            PermitStatusesEnum::third->value,
            PermitStatusesEnum::fifth->value,
        ];

        $uploadPlanStatuses = [
            PermitStatusesEnum::eighth->value,
            PermitStatusesEnum::failed->value,
        ];

        $uploadObligation = [
            PermitStatusesEnum::tenth->value,
        ];

        if (in_array($lastStatusName, $uploadFormStatuses, true)) {
            $this->uploadFilesToForms($id, $fileID, $fileName, $user);
        }

        if (in_array($lastStatusName, $uploadPlanStatuses, true)) {
            $this->uploadFilesToPlans($id, $fileID, $fileName, $user);
        }

        if (in_array($lastStatusName, $uploadObligation, true)) {
            $this->uploadFilesToObligation($id, $fileID, $fileName, $user);
        }
    }

    public
    function uploadFilesToForms($dossierID, $fileID, $fileName, $user)
    {
        $form = Form::create([
            'file_id' => $fileID,
            'creator_id' => $user->id,
            'created_date' => now(),
        ]);

        LicenseDocument::create([
            'dossier_id' => $dossierID,
            'documentable_id' => $form->id,
            'documentable_type' => Form::class,
            'name' => $fileName,
        ]);
    }

    public
    function uploadFilesToPlans($dossierID, $fileID, $fileName, $user)
    {
        $license = LicenseDocument::where('dossier_id', $dossierID)
            ->where('documentable_type', Plan::class)
            ->first();
        if ($license) {
            $plan = Plan::find($license->documentable_id);
            $plan->update([
                'file_id' => $fileID,
            ]);
        } else {
            $plan = Plan::create([
                'file_id' => $fileID,
                'creator_id' => $user->id,
                'created_date' => now(),
            ]);

            LicenseDocument::create([
                'dossier_id' => $dossierID,
                'documentable_id' => $plan->id,
                'documentable_type' => Plan::class,
                'name' => $fileName,
            ]);
        }

    }

    public
    function uploadFilesToObligation($dossierID, $fileID, $fileName, $user)
    {

        $license = LicenseDocument::where('dossier_id', $dossierID)
            ->where('name', $fileName)
            ->where('documentable_type', Obligation::class)
            ->first();
        if ($license) {
            $obligation = Obligation::where('id', $license->documentable_id)->first();
            $obligation->update([
                'file_id' => $fileID,
            ]);
        } else {
            $obligation = Obligation::create([
                'file_id' => $fileID,
                'creator_id' => $user->id,
                'created_date' => now(),
            ]);

            LicenseDocument::create([
                'dossier_id' => $dossierID,
                'documentable_id' => $obligation->id,
                'documentable_type' => Obligation::class,
                'name' => $fileName,
            ]);
        }
    }


    public
    function getUploadedFiles($statusName, $dossierID)
    {
        $files = [];

        if ($statusName == PermitStatusesEnum::fifth->value) {
            $query = BuildingDossier::join('bdm_license_documents', function ($join) {
                $join->on('bdm_building_dossiers.id', '=', 'bdm_license_documents.dossier_id')
                    ->where('bdm_license_documents.documentable_type', '=', Form::class)
                    ->where('bdm_license_documents.name', '=', DocumentsNameEnum::FORM_ALEF_ONE_TWO->value);
            })
                ->join('bdm_forms', function ($join) use ($dossierID) {
                    $join->on('bdm_license_documents.documentable_id', '=', 'bdm_forms.id');
                })
                ->join('files', 'bdm_forms.file_id', '=', 'files.id')
                ->join('extensions', 'files.extension_id', '=', 'extensions.id')
                ->select([
                    'files.slug as file_slug',
                    'bdm_license_documents.name as name',
                    'files.size as size',
                    'extensions.name as extension_name',
                ])
                ->first();

            $files[] = $query;
        }
        return $files;

    }

    public
    function makeDossierDeclined($data, $dossierID)
    {
        $status = $this->findCurrentPermitStatusOfDossier($dossierID);
        $statusName = $status->permit_status_name;


        $user = Auth::user();
        if ($statusName == PermitStatusesEnum::ninth->value) {
            PermitStatus::create([
                'dossier_id' => $dossierID,
                'status_id' => $this->failedStatus()->id,
                'created_date' => now(),
                'creator_id' => $user->id,
                'description' => $data['description'],
                'file_id' => $data['fileID'],
            ]);
        }

        if ($statusName == PermitStatusesEnum::eleventh->value) {
            PermitStatus::create([
                'dossier_id' => $dossierID,
                'status_id' => $this->rejectObligationsStatus()->id,
                'created_date' => now(),
                'creator_id' => $user->id,
                'description' => $data['description'] ?? null,
                'file_id' => $data['fileID'] ?? null,
            ]);

            $documents = LicenseDocument::where('dossier_id', $dossierID)
                ->where('documentable_type', Obligation::class)
                ->get();

            foreach ($documents as $document) {
                Obligation::where('id', $document->documentable_id)->delete();
            }

            LicenseDocument::destroy($documents->pluck('id'));
        }
    }

    public
    function getStructures($dossierID)
    {
        $building = Structure::join('bdm_building', function ($join) {
            $join->on('bdm_building.id', '=', 'bdm_structures.structureable_id')
                ->where('bdm_structures.structureable_type', '=', Building::class);
        })
            ->select([
                'bdm_structures.id as structure_id',
                'bdm_building.id as building_id',
                'bdm_building.app_id',
                'bdm_building.floor_type_id',
                'bdm_building.floor_number_id',
                'bdm_building.all_corbelling_area',
                'bdm_building.floor_height',
                'bdm_building.building_area',
                'bdm_building.storage_area',
                'bdm_building.stairs_area',
                'bdm_building.elevator_shaft',
                'bdm_building.parking_area',
                'bdm_building.corbelling_area',
                'bdm_building.duct_area',
                'bdm_building.other_parts_area',
                'bdm_building.is_existed',
            ])
            ->where('bdm_structures.dossier_id', $dossierID)
            ->get();
        $building->map(function ($item) {
            $item->floor_number_name = FloorNumbersEnum::getNameById($item->floor_number_id);
            $item->floor_type_name = LevyItem::find((int)$item->floor_type_id)->name;

            $item->app_name = Application::find($item->app_id)->name;
        });

        $pool = Structure::join('bdm_pools', function ($join) {
            $join->on('bdm_pools.id', '=', 'bdm_structures.structureable_id')
                ->where('bdm_structures.structureable_type', '=', Pool::class);
        })
            ->select([
                'bdm_structures.id as structure_id',
                'bdm_pools.id as pool_id',
                'bdm_pools.height',
                'bdm_pools.width',
                'bdm_pools.length',
                'bdm_pools.app_id',
            ])
            ->where('bdm_structures.dossier_id', $dossierID)
            ->get();
        if ($pool) {
            $pool->map(function ($item) {
                $item->app_name = Application::find($item->app_id)->name;
                $item->type = "استخر";
            });
        }
        $pavilion = Structure::join('bdm_pavilion', function ($join) {
            $join->on('bdm_pavilion.id', '=', 'bdm_structures.structureable_id')
                ->where('bdm_structures.structureable_type', '=', Pavilion::class);
        })
            ->select([
                'bdm_structures.id as structure_id',
                'bdm_pavilion.id as pavilion_id',
                'bdm_pavilion.height',
                'bdm_pavilion.width',
                'bdm_pavilion.length',
                'bdm_pavilion.app_id',
            ])
            ->where('bdm_structures.dossier_id', $dossierID)
            ->get();
        if ($pavilion) {
            $pavilion->map(function ($item) {
                $item->app_name = Application::find($item->app_id)->name;
                $item->type = "آلاچیق";
            });
        }

        $parking = Structure::join('bdm_parking', function ($join) {
            $join->on('bdm_parking.id', '=', 'bdm_structures.structureable_id')
                ->where('bdm_structures.structureable_type', '=', Parking::class);
        })
            ->select([
                'bdm_structures.id as structure_id',
                'bdm_parking.id as parking_id',
                'bdm_parking.height',
                'bdm_parking.length',
                'bdm_parking.width',
                'bdm_parking.app_id',
            ])
            ->where('bdm_structures.dossier_id', $dossierID)
            ->get();
        if ($parking) {
            $parking->map(function ($item) {
                $item->app_name = Application::find($item->app_id)->name;
                $item->type = "پارکینگ";
            });
        }
        $partitioning = Structure::join('bdm_partitioning', function ($join) {
            $join->on('bdm_partitioning.id', '=', 'bdm_structures.structureable_id')
                ->where('bdm_structures.structureable_type', '=', Partitioning::class);
        })
            ->select([
                'bdm_structures.id as structure_id',
                'bdm_partitioning.id as partitioning_id',
                'bdm_partitioning.height',
                'bdm_partitioning.partitioning_type_id',
                'bdm_partitioning.app_id',
            ])
            ->where('bdm_structures.dossier_id', $dossierID)
            ->get();
        if ($partitioning) {

            $partitioning->map(function ($item) {
                $item->app_name = Application::find($item->app_id)->name;
                $item->partitioning_type_name = LevyItem::find((int)$item->partitioning_type_id)->name;
            });
        }
        return ["buildings" => $building, "pools" => $pool, "pavilions" => $pavilion, "parkings" => $parking, "partitionings" => $partitioning];
    }

    public
    function getButtons()
    {
        return [
            PermitStatusesEnum::first->value => ['submit', 'archive'],
            PermitStatusesEnum::second->value => [],
            PermitStatusesEnum::third->value => ['upload'],
            PermitStatusesEnum::fourth->value => ['completeEstate', 'submit'],
            PermitStatusesEnum::fifth->value => ['submit', 'upload'],
            PermitStatusesEnum::sixth->value => [],
            PermitStatusesEnum::seventh->value => ['addEngineers'],
            PermitStatusesEnum::eighth->value => ['submit', 'upload'],
            PermitStatusesEnum::ninth->value => ['decline', 'completeAndSubmit'],
            PermitStatusesEnum::tenth->value => ['upload'],
            PermitStatusesEnum::eleventh->value => ['editPromises', 'publishBill'],
            PermitStatusesEnum::twelfth->value => ['payment'],
            PermitStatusesEnum::thirteenth->value => [],
            PermitStatusesEnum::fourteenth->value => [],
            PermitStatusesEnum::fifteenth->value => [],
            PermitStatusesEnum::sixteenth->value => [],
            PermitStatusesEnum::seventeenth->value => [],
            PermitStatusesEnum::eighteenth->value => [],
            PermitStatusesEnum::nineteenth->value => ['submit'],
            PermitStatusesEnum::twentieth->value => ['print'],
            PermitStatusesEnum::twentyfirst->value => [],
        ];
    }

    public
    function getEngineers($dossierID)
    {
        $query = BuildingDossier::join('bdm_engineers_building', 'bdm_engineers_building.dossier_id', '=', 'bdm_building_dossiers.id')
            ->join('bdm_engineers', 'bdm_engineers_building.engineer_id', '=', 'bdm_engineers.id')
            ->join('persons', 'bdm_engineers.person_id', '=', 'persons.id')
            ->join('naturals', function ($join) {
                $join->on('persons.personable_id', '=', 'naturals.id')
                    ->where('persons.personable_type', '=', Natural::class);
            })
            ->join('bdm_license_documents', function ($join) {
                $join->on('bdm_license_documents.dossier_id', '=', 'bdm_building_dossiers.id')
                    ->where('bdm_license_documents.documentable_type', '=', Obligation::class);
            })
            ->join('bdm_obligations', 'bdm_obligations.id', '=', 'bdm_license_documents.documentable_id')
            ->join('files', 'bdm_obligations.file_id', '=', 'files.id')
            ->join('files as working_files', 'working_files.id', '=', 'bdm_engineers.working_file_id')
            ->select([
                'persons.display_name',
                'persons.national_code',
                'naturals.mobile',
                'naturals.father_name',
                'naturals.gender_id',
                'bdm_engineers_building.engineer_type_id',
                'files.slug as slug',
                'working_files.slug as working_slug',
            ]);

        $query->when(request('engineer_type_id') == EngineersTypeEnum::MOHASEB->id(), function ($q) {
            $q->where('bdm_license_documents.name', DocumentsNameEnum::FORM_SEVEN->value);
        })->when(request('engineer_type_id') == EngineersTypeEnum::MEMAR->id(), function ($q) {
            $q->where('bdm_license_documents.name', DocumentsNameEnum::FORM_FIVE->value);
        })->when(request('engineer_type_id') == EngineersTypeEnum::NAZER->id(), function ($q) {
            $q->where('bdm_license_documents.name', DocumentsNameEnum::FORM_SIX->value);
        });
        $query->where('bdm_building_dossiers.id', $dossierID);
        $query->distinct();
        $engineers = $query->get();

        $engineers->map(function ($engineer) {
            $engineer->slug = url($engineer->slug);
            $engineer->working_slug = url($engineer->working_slug);
            $engineer->engineer_type_name = EngineersTypeEnum::getNameById($engineer->engineer_type_id);
        });

        return $engineers;
    }

    public
    function getPayments($id)
    {
        $Dossier = BuildingDossier::find($id);
        if ($Dossier->bill_id != null) {
            $billID = $Dossier->bill_id;
            return $this->getBillData($billID);
        } else {
            return null;
        }
    }

    public
    function getPlan($id)
    {
        $query = BuildingDossier::join('bdm_license_documents', function ($join) {
            $join->on('bdm_license_documents.dossier_id', '=', 'bdm_building_dossiers.id')
                ->where('bdm_license_documents.documentable_type', '=', Plan::class);
        })
            ->join('bdm_plans', 'bdm_license_documents.documentable_id', '=', 'bdm_plans.id')
            ->join('files', 'bdm_plans.file_id', '=', 'files.id')
            ->leftJoin('bdm_building_permit_status', function ($join) {
                $join->on('bdm_building_permit_status.dossier_id', '=', 'bdm_building_dossiers.id')
                    ->join('statuses', function ($join) {
                        $join->on('bdm_building_permit_status.status_id', '=', 'statuses.id')
                            ->where('statuses.name', '=', PermitStatusesEnum::tenth->value);
                    });
            })
            ->leftJoin('users', function ($join) {
                $join->on('users.id', '=', 'bdm_building_permit_status.creator_id');
            })
            ->leftJoin('persons', 'users.person_id', '=', 'persons.id')
            ->select([
                'bdm_building_dossiers.id as dossier_id',
                'bdm_building_permit_status.status_id as permit_status_id',
                'bdm_building_permit_status.created_date as permit_status_created_date',
                'persons.display_name as permit_status_creator_name',
                'bdm_license_documents.name as license_name',
                'files.slug as license_file_slug',
            ])
            ->find($id);
        if ($query) {
            $query->license_file_slug = url($query->license_file_slug);
        }
        return $query;
    }

    public
    function getBuildingBills($id)
    {
        $building = BuildingDossier::join('bdm_structures', function ($join) {
            $join->on('bdm_structures.dossier_id', '=', 'bdm_building_dossiers.id')
                ->where('bdm_structures.structureable_type', '=', Building::class);
        })
            ->join('bdm_building', 'bdm_building.id', '=', 'bdm_structures.structureable_id')
            ->join('pfm_levy_items', 'pfm_levy_items.id', '=', 'bdm_building.floor_type_id')
            ->join('bdm_estates', 'bdm_estates.dossier_id', '=', 'bdm_building_dossiers.id')
            ->join('pfm_levy_circular', 'pfm_levy_circular.id', '=', 'pfm_levy_items.circular_levy_id')
            ->join('pfm_circular_booklets', function ($join) {
                $join->on('pfm_circular_booklets.pfm_circular_id', '=', 'pfm_levy_circular.circular_id')
                    ->on('pfm_circular_booklets.ounit_id', '=', 'bdm_estates.ounit_id');
            })
            ->join('pfm_levies', 'pfm_levies.id', '=', 'pfm_levy_circular.levy_id')
            ->join('pfm_circular_tariffs', function ($join) {
                $join->on('pfm_circular_tariffs.item_id', '=', 'pfm_levy_items.id')
                    ->when('pfm_levies.has_app', function ($query) {
                        $query->on('pfm_circular_tariffs.app_id', '=', 'bdm_building.app_id');
                    });
            })
            ->join('pfm_prop_applications', function ($join) {
                $join->on('pfm_prop_applications.id', '=', 'bdm_building.app_id');
            })
            ->select([
                'bdm_building_dossiers.tracking_code',
                'bdm_building.id as building_id',
                'bdm_building.app_id',
                'bdm_building.floor_number_id',
                'bdm_building.floor_height',
                'bdm_building.building_area',
                'bdm_building.is_existed',
                'bdm_building.parking_area',
                'bdm_building.storage_area',
                'bdm_building.stairs_area',
                'bdm_building.elevator_shaft',
                'bdm_building.other_parts_area',
                'bdm_building.duct_area',
                'bdm_building.corbelling_area',
                'pfm_levy_items.id as item_id',
                'pfm_levy_items.name as item_name',
                'bdm_estates.ounit_id',
                'pfm_levy_circular.circular_id',
                'pfm_circular_booklets.p_residential',
                'pfm_circular_booklets.p_commercial',
                'pfm_circular_booklets.p_administrative',
                'pfm_circular_tariffs.value',
                'pfm_prop_applications.main_prop_type',
                'pfm_prop_applications.adjustment_coefficient'
            ])
            ->distinct()
            ->where('bdm_building_dossiers.id', $id)
            ->get();
        $building->map(function ($item) {
            $item->floor_number_name = FloorNumbersEnum::getNameById($item->floor_number_id);
            if ($item->main_prop_type == ApplicationsCoefficientEnum::residential->value) {
                $item->price = $item->p_residential * $item->building_area * $item->value * $item->adjustment_coefficient;
                $corebellingAreaItem = LevyItem::where('name', "پیش آمدگی به صورت ساختمان")->orderBy('id', 'desc')->first();
                $tariff = Tarrifs::where('item_id', $corebellingAreaItem->id)
                    ->where('app_id', $item->app_id)
                    ->first();
                $item->price += $tariff->value * $item->p_residential * $item->corbelling_area;
            } else if ($item->main_prop_type == ApplicationsCoefficientEnum::commercial->value) {
                $item->price = $item->p_commercial * $item->building_area * $item->value * $item->adjustment_coefficient;
                $corebellingAreaItem = LevyItem::where('name', "پیش آمدگی به صورت ساختمان")->orderBy('id', 'desc')->first();
                $tariff = Tarrifs::where('item_id', $corebellingAreaItem->id)
                    ->where('app_id', $item->app_id)
                    ->first();
                $item->price += $tariff->value * $item->p_commercial * $item->corbelling_area;
            } else if ($item->main_prop_type == ApplicationsCoefficientEnum::administrative->value) {
                $item->price = $item->p_administrative * $item->building_area * $item->value * $item->adjustment_coefficient;
                $corebellingAreaItem = LevyItem::where('name', "پیش آمدگی به صورت ساختمان")->orderBy('id', 'desc')->first();
                $tariff = Tarrifs::where('item_id', $corebellingAreaItem->id)
                    ->where('app_id', $item->app_id)
                    ->first();
                $item->price += $tariff->value * $item->p_administrative * $item->corbelling_area;
            }
        });

        $totalPrice = $building->sum('price');
        $totalArea = $building->sum('building_area');
        $totalHeight = $building->sum('floor_height');
        return [
            'buildings' => $building,
            'total_price' => $totalPrice,
            'total_area' => $totalArea,
            'total_height' => $totalHeight,
        ];
    }

    public
    function getPartitioningBills($id)
    {
        $pools = BuildingDossier::join('bdm_structures', function ($join) {
            $join->on('bdm_structures.dossier_id', '=', 'bdm_building_dossiers.id')
                ->where('bdm_structures.structureable_type', '=', Partitioning::class);
        })
            ->join('bdm_estates', 'bdm_estates.dossier_id', '=', 'bdm_building_dossiers.id')
            ->join('bdm_partitioning', 'bdm_partitioning.id', '=', 'bdm_structures.structureable_id')
            ->join('pfm_levy_items', 'pfm_levy_items.id', '=', 'bdm_partitioning.partitioning_type_id')
            ->join('pfm_levy_circular', 'pfm_levy_circular.id', '=', 'pfm_levy_items.circular_levy_id')
            ->join('pfm_circular_booklets', function ($join) {
                $join->on('pfm_circular_booklets.pfm_circular_id', '=', 'pfm_levy_circular.circular_id')
                    ->on('pfm_circular_booklets.ounit_id', '=', 'bdm_estates.ounit_id');
            })
            ->join('pfm_circular_tariffs', function ($join) {
                $join->on('pfm_circular_tariffs.item_id', '=', 'pfm_levy_items.id')
                    ->when('pfm_levy_circular.has_app', function ($query) {
                        $query->on('pfm_circular_tariffs.app_id', '=', 'bdm_partitioning.app_id');
                    });
            })
            ->join('pfm_prop_applications', function ($join) {
                $join->on('pfm_prop_applications.id', '=', 'bdm_partitioning.app_id');
            })
            ->select([
                'bdm_structures.id as structure_id',
                'bdm_partitioning.id as pool_id',
                'bdm_partitioning.height',
                'bdm_partitioning.partitioning_type_id',
                'pfm_circular_tariffs.value',
                'pfm_circular_booklets.p_residential',
                'pfm_circular_booklets.p_commercial',
                'pfm_circular_booklets.p_administrative',
                'pfm_circular_tariffs.value',
                'pfm_prop_applications.main_prop_type',
                'pfm_prop_applications.adjustment_coefficient',
            ])
            ->where('bdm_structures.dossier_id', $id)
            ->distinct()
            ->get();

        $pools->map(function ($item) {
            $item->partition_name = LevyItem::where('id', $item->partitioning_type_id)->first()->name;
            if ($item->main_prop_type == ApplicationsCoefficientEnum::residential->value) {
                $item->price = $item->value * $item->height->$item->p_residential * $item->adjustment_coefficient;
            }
            if ($item->main_prop_type == ApplicationsCoefficientEnum::commercial->value) {
                $item->price = $item->value * $item->height * $item->p_commercial * $item->adjustment_coefficient;

            }
            if ($item->main_prop_type == ApplicationsCoefficientEnum::administrative->value) {
                $item->price = $item->value * $item->height->$item->p_administrative * $item->adjustment_coefficient;
            }
        });
        $totalPrice = $pools->sum('price');

        return [
            'partition' => $pools,
            'total_price' => $totalPrice
        ];
    }

    public
    function getPoolBills($dossierID)
    {
        $pool = BuildingDossier::join('bdm_structures', function ($join) {
            $join->on('bdm_structures.dossier_id', '=', 'bdm_building_dossiers.id')
                ->where('bdm_structures.structureable_type', '=', Pavilion::class);
        })
            ->join('bdm_estates', 'bdm_estates.dossier_id', '=', 'bdm_building_dossiers.id')
            ->join('bdm_pools', 'bdm_pools.id', '=', 'bdm_structures.structureable_id')
            ->join('pfm_levy_items', function ($join) {
                $join->where('pfm_levy_items.name', '=', 'استخر');
            })
            ->join('pfm_circular_tariffs', function ($join) {
                $join->on('pfm_circular_tariffs.item_id', '=', 'pfm_levy_items.id')
                    ->on('pfm_circular_tariffs.app_id', '=', 'bdm_pools.app_id');
            })
            ->join('pfm_levy_circular', 'pfm_levy_circular.id', '=', 'pfm_levy_items.circular_levy_id')
            ->join('pfm_circular_booklets', function ($join) {
                $join->on('pfm_circular_booklets.ounit_id', '=', 'bdm_estates.ounit_id')
                    ->on('pfm_circular_booklets.pfm_circular_id', '=', 'pfm_levy_circular.circular_id');
            })
            ->join('pfm_prop_applications', function ($join) {
                $join->on('pfm_prop_applications.id', '=', 'bdm_pools.app_id');
            })
            ->select([
                'bdm_pools.id as pool_id',
                'bdm_pools.height',
                'bdm_pools.width',
                'bdm_pools.length',
                'pfm_levy_circular.circular_id',
                'pfm_levy_items.id as item_id',
                'pfm_circular_booklets.p_residential',
                'pfm_circular_booklets.p_commercial',
                'pfm_circular_booklets.p_administrative',
                'pfm_circular_tariffs.value',
                'pfm_prop_applications.main_prop_type',
                'pfm_prop_applications.adjustment_coefficient',
            ])
            ->where('bdm_structures.dossier_id', $dossierID)
            ->distinct()
            ->get();

        $pool->map(function ($item) {
            if ($item->main_prop_type == ApplicationsCoefficientEnum::residential->value) {
                $item->price = $item->value * $item->length * $item->p_residential * $item->height * $item->width;
            }
            if ($item->main_prop_type == ApplicationsCoefficientEnum::commercial->value) {
                $item->price = $item->value * $item->length * $item->p_commercial * $item->height * $item->width;

            }
            if ($item->main_prop_type == ApplicationsCoefficientEnum::administrative->value) {
                $item->price = $item->value * $item->length * $item->p_administrative * $item->height * $item->width;

            }
        });
        $totalPrice = $pool->sum('price');

        return [
            'pools' => $pool,
            'total_price' => $totalPrice
        ];
    }

    public
    function getPavilionBills($dossierID)
    {
        $pavilion = BuildingDossier::join('bdm_structures', function ($join) {
            $join->on('bdm_structures.dossier_id', '=', 'bdm_building_dossiers.id')
                ->where('bdm_structures.structureable_type', '=', Pavilion::class);
        })
            ->join('bdm_estates', 'bdm_estates.dossier_id', '=', 'bdm_building_dossiers.id')
            ->join('bdm_pools', 'bdm_pools.id', '=', 'bdm_structures.structureable_id')
            ->join('pfm_levy_items', function ($join) {
                $join->where('pfm_levy_items.name', '=', 'آلاچیق');
            })
            ->join('pfm_circular_tariffs', function ($join) {
                $join->on('pfm_circular_tariffs.item_id', '=', 'pfm_levy_items.id')
                    ->on('pfm_circular_tariffs.app_id', '=', 'bdm_pools.app_id');
            })
            ->join('pfm_levy_circular', 'pfm_levy_circular.id', '=', 'pfm_levy_items.circular_levy_id')
            ->join('pfm_circular_booklets', function ($join) {
                $join->on('pfm_circular_booklets.ounit_id', '=', 'bdm_estates.ounit_id')
                    ->on('pfm_circular_booklets.pfm_circular_id', '=', 'pfm_levy_circular.circular_id');
            })
            ->join('pfm_prop_applications', function ($join) {
                $join->on('pfm_prop_applications.id', '=', 'bdm_pools.app_id');
            })
            ->select([
                'bdm_pools.id as pool_id',
                'bdm_pools.height',
                'bdm_pools.width',
                'bdm_pools.length',
                'pfm_levy_circular.circular_id',
                'pfm_levy_items.id as item_id',
                'pfm_circular_booklets.p_residential',
                'pfm_circular_booklets.p_commercial',
                'pfm_circular_booklets.p_administrative',
                'pfm_circular_tariffs.value',
                'pfm_prop_applications.main_prop_type',
                'pfm_prop_applications.adjustment_coefficient',
            ])
            ->where('bdm_structures.dossier_id', $dossierID)
            ->distinct()
            ->get();

        $pavilion->map(function ($item) {
            if ($item->main_prop_type == ApplicationsCoefficientEnum::residential->value) {
                $item->price = $item->value * $item->length * $item->p_residential * $item->height * $item->width;
            }
            if ($item->main_prop_type == ApplicationsCoefficientEnum::commercial->value) {
                $item->price = $item->value * $item->length * $item->p_commercial * $item->height * $item->width;

            }
            if ($item->main_prop_type == ApplicationsCoefficientEnum::administrative->value) {
                $item->price = $item->value * $item->length * $item->p_administrative * $item->height * $item->width;

            }
        });
        $totalPrice = $pavilion->sum('price');

        return [
            'pavilion' => $pavilion,
            'total_price' => $totalPrice
        ];
    }

    public
    function getParkingBills($dossierID)
    {
        $parking = BuildingDossier::join('bdm_structures', function ($join) {
            $join->on('bdm_structures.dossier_id', '=', 'bdm_building_dossiers.id')
                ->where('bdm_structures.structureable_type', '=', Pavilion::class);
        })
            ->join('bdm_estates', 'bdm_estates.dossier_id', '=', 'bdm_building_dossiers.id')
            ->join('bdm_pools', 'bdm_pools.id', '=', 'bdm_structures.structureable_id')
            ->join('pfm_levy_items', function ($join) {
                $join->where('pfm_levy_items.name', '=', 'پارکینگ مسقف');
            })
            ->join('pfm_circular_tariffs', function ($join) {
                $join->on('pfm_circular_tariffs.item_id', '=', 'pfm_levy_items.id')
                    ->on('pfm_circular_tariffs.app_id', '=', 'bdm_pools.app_id');
            })
            ->join('pfm_levy_circular', 'pfm_levy_circular.id', '=', 'pfm_levy_items.circular_levy_id')
            ->join('pfm_circular_booklets', function ($join) {
                $join->on('pfm_circular_booklets.ounit_id', '=', 'bdm_estates.ounit_id')
                    ->on('pfm_circular_booklets.pfm_circular_id', '=', 'pfm_levy_circular.circular_id');
            })
            ->join('pfm_prop_applications', function ($join) {
                $join->on('pfm_prop_applications.id', '=', 'bdm_pools.app_id');
            })
            ->select([
                'bdm_pools.id as pool_id',
                'bdm_pools.height',
                'bdm_pools.width',
                'bdm_pools.length',
                'pfm_levy_circular.circular_id',
                'pfm_levy_items.id as item_id',
                'pfm_circular_booklets.p_residential',
                'pfm_circular_booklets.p_commercial',
                'pfm_circular_booklets.p_administrative',
                'pfm_circular_tariffs.value',
                'pfm_prop_applications.main_prop_type',
                'pfm_prop_applications.adjustment_coefficient',
            ])
            ->where('bdm_structures.dossier_id', $dossierID)
            ->distinct()
            ->get();

        $parking->map(function ($item) {
            if ($item->main_prop_type == ApplicationsCoefficientEnum::residential->value) {
                $item->price = $item->value * $item->length * $item->p_residential * $item->height * $item->width;
            }
            if ($item->main_prop_type == ApplicationsCoefficientEnum::commercial->value) {
                $item->price = $item->value * $item->length * $item->p_commercial * $item->height * $item->width;

            }
            if ($item->main_prop_type == ApplicationsCoefficientEnum::administrative->value) {
                $item->price = $item->value * $item->length * $item->p_administrative * $item->height * $item->width;

            }
        });
        $totalPrice = $parking->sum('price');

        return [
            'parking' => $parking,
            'total_price' => $totalPrice
        ];
    }

    public
    function getBankAccs($dossierID)
    {
        $query = BuildingDossier::join('bdm_estates', 'bdm_estates.dossier_id', '=', 'bdm_building_dossiers.id')
            ->join('bnk_bank_accounts', 'bnk_bank_accounts.ounit_id', '=', 'bdm_estates.ounit_id')
            ->join('bnk_bank_branches', 'bnk_bank_branches.id', '=', 'bnk_bank_accounts.branch_id')
            ->join('bnk_banks', 'bnk_banks.id', '=', 'bnk_bank_branches.bank_id')
            ->select([
                'bdm_building_dossiers.id as dossier_id',
                'bnk_banks.name as bank_name',
                'bnk_bank_accounts.*',
            ])
            ->where('bdm_building_dossiers.id', $dossierID)
            ->get();
        return $query;
    }

    public
    function getPermitStatusesList()
    {
        $query = Status::where('model', PermitStatus::class)->select(['id', 'name'])->get();

        $data = [];

        foreach ($query as $item) {
            $state = PermitStatusesEnum::whichNumberStatic($item->name);
            $description = PermitStatusesEnum::getDescriptionByName($item->name);
            if ($state != null) {
                $data[] = [
                    'id' => $item->id,
                    'title' => $state . ': ' . $item->name,
                    'description' => $description
                ];
            }
        }
        return $data;
    }

    public
    function publishingDossierBill($data, $id)
    {
        $bill = Bill::create([
            'bank_account_id' => $data['bank_account_id'],
        ]);


        $dosser = BuildingDossier::find($id);
        $estate = Estate::where('dossier_id', $id)->first();
        $ounitID = $estate->ounit_id;
        $dosser->bill_id = $bill->id;
        $dosser->save();


        $levyItem = LevyItem::where('name', LeviesListEnum::SUDURE_PARVANEH_SAKHTEMAN->value)->orderBy('id', 'desc')->first();

        $booklet = Booklet::where('ounit_id', $ounitID)->orderBy('id', 'desc')->first();


        $tariff = Tarrifs::where('booklet_id', $booklet->id)
            ->where('item_id', $levyItem->id)
            ->first();


        $billTarif = BillTariff::create([
            'bill_id' => $bill->id,
            'tariff_id' => $tariff->id,
        ]);

        BillItemProperty::create([
            'bill_tariff_id' => $billTarif->id,
            'key' => 'value',
            'value' => 'value',
        ]);

        $owner = BuildingDossier::join('bdm_owners', 'bdm_building_dossiers.id', '=', 'bdm_owners.dossier_id')
            ->select([
                'bdm_owners.person_id',
            ])
            ->where('bdm_owners.is_main_owner', '=', true)
            ->find($id);
        $personID = $owner->person_id;
        $person = Person::find($personID);
        $user = Auth::user();

        if (!isset($data['discountAmount'])) {
            $data['discountAmount'] = 0;
        }

        $payment = new PaymentService($bill->id, $user, $data['price'], $data['maxDays'], $data['discountAmount'], $person);
        $payment->makeUserCustomer();
        $payment->generateBill();
        return $bill;
    }

    public
    function StoreBdmFormA1_2($id)
    {


        $this->findBDMApprovers($id);


        $data = [
            'component_to_render' => 'dossier_form_1_2',
            'model_id' => $id,
            'title' => DocumentsNameEnum::FORM_ALEF_ONE_TWO->value,
            'created_date' => \Illuminate\Support\Carbon::now(),
            'status_id' => $this->PendingOdocDocumentStatus()->id,
            'status_description' => null,
            'approvers' => [
                [
                    'person_id' => 45,
                    'status_id' => $this->AssignedApproversStatus()->id,
                    'signed_date' => Carbon::now(),
                    'token' => null,
                    'signature_id' => null,
                    'document_id' => null,
                ],
                [
                    'person_id' => 46,
                    'status_id' => $this->PendingApproversStatus()->id,
                    'signed_date' => Carbon::now(),
                    'token' => null,
                    'signature_id' => null,
                    'document_id' => null,
                ],
            ]
        ];
        $user = User::find(2174);
        $this->storeOdocDocument($data, $user->id);
    }

    private
    function findBDMApprovers($id)
    {
        $status = $this->findCurrentPermitStatusOfDossier($id);
        if ($status->permit_status_name == PermitStatusesEnum::first->value) {
            $this->BdmApproversProccess($id);
        }
    }

    private
    function BdmApproversProccess($id)
    {
        $query = BuildingDossier::join('bdm_estates', 'bdm_building_dossiers.id', '=', 'bdm_estates.dossier_id')
            ->join('organization_units as village', 'bdm_estates.ounit_id', '=', 'village.id')
            ->join('users', 'organization_uints.head_id', '=', 'users.id')
            ->select([
                'users.person_id',
            ])
            ->find($id);
        $personID = $query->person_id;
    }

}
