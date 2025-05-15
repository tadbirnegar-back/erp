<?php

namespace Modules\BDM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\AAA\app\Http\Traits\UserTrait;
use Modules\AAA\app\Models\User;
use Modules\BDM\app\Http\Enums\BdmOwnershipTypesEnum;
use Modules\BDM\app\Http\Enums\BdmTypesEnum;
use Modules\BDM\app\Http\Enums\DocumentsNameEnum;
use Modules\BDM\app\Http\Enums\DossierStatusesEnum;
use Modules\BDM\app\Http\Enums\PermitStatusesEnum;
use Modules\BDM\app\Http\Enums\TransferTypesEnum;
use Modules\BDM\app\Http\Traits\DossierTrait;
use Modules\BDM\app\Http\Traits\EstateTrait;
use Modules\BDM\app\Http\Traits\LawyersTrait;
use Modules\BDM\app\Http\Traits\OwnersTrait;
use Modules\BDM\app\Models\BuildingDossier;
use Modules\BDM\app\Models\DossierStatus;
use Modules\BDM\app\Models\PermitStatus;
use Modules\BDM\app\Resources\LicensesListResource;
use Modules\HRMS\app\Http\Enums\RecruitmentScriptStatusEnum;
use Modules\HRMS\app\Http\Enums\ScriptTypesEnum;
use Modules\HRMS\app\Models\ExemptionType;
use Modules\HRMS\app\Models\MilitaryService;
use Modules\HRMS\app\Models\MilitaryServiceStatus;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Models\ScriptType;
use Modules\ODOC\app\Http\Traits\OdocApproversTrait;
use Modules\ODOC\app\Http\Traits\OdocDocemntTrait;
use Modules\OUnitMS\app\Models\CityOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\VillageOfc;
use Modules\PersonMS\app\Http\Traits\PersonTrait;
use Modules\PersonMS\app\Models\Natural;
use Modules\PersonMS\app\Models\Person;
use Modules\PFM\app\Models\Application;
use Modules\StatusMS\app\Models\Status;

class LicenseController extends Controller
{
    use PersonTrait, UserTrait, DossierTrait, OwnersTrait, LawyersTrait, EstateTrait , OdocDocemntTrait , OdocApproversTrait;

    public function licenseTypesList()
    {
        $bdmTypes = BdmTypesEnum::listWithIds();
        $ownershipTypes = BdmOwnershipTypesEnum::listWithIds();
        $transferTypes = TransferTypesEnum::listWithIds();
        $cities = OrganizationUnit::where('unitable_type', CityOfc::class)->select(['id', 'name'])->get();
        $militaryServices = MilitaryServiceStatus::get();
        $apps = Application::select('id', 'name')->get();

        return response()->json(["dossierTypes" => $bdmTypes, "ownershipTypes" => $ownershipTypes, "transferTypes" => $transferTypes, "cities" => $cities, "militaryServices" => $militaryServices, "apps" => $apps]);
    }

    public function makeArchive($id)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();
            DossierStatus::create([
                'dossier_id' => $id,
                'status_id' => $this->archiveStatus()->id,
                'created_date' => now(),
                'creator_id' => $user->id,
            ]);
            Db::commit();
            return response()->json(['message' => 'بایگانی با موفقیت انجام شد']);
        }catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }

    }

    public function create(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $dossier = $this->makeDossier($data['ounitID'], $data['ownershipTypeID'], $data['bdmTypeID']);

            $password = '';
            $mobile = '';
            //store owners and partners
            $persons = json_decode($data['persons']);

            foreach ($persons as $key => $person) {

                $createdOrUpdatedPerson = $this->personUpdateOrInsert($person);
                if (isset($createdOrUpdatedPerson['type'])) {
                    return response()->json(['message' => 'شماره موبایل قبلا در سامانه ثبت شده'], 404);
                }
                $personId = $createdOrUpdatedPerson->id;
                $this->insertLicenses($personId, $person);
                if ($key == 0) {
                    $person->personID = $personId;
                    $person->password = $person->nationalCode;
                    $password = $person->password;
                    $mobile = $person->mobile;
                    $user = $this->storeUserOrUpdate((array)$person);
                    if ($user['status'] == 404) {
                        DB::rollBack();
                        if ($user['type'] == 'mobile') {
                            return response()->json(['message' => 'شماره موبایل قبلا در سامانه ثبت شده'], 404);
                        } else {
                            return response()->json(['message' => 'ایمیل قبلا در سامانه ثبت شده'], 404);
                        }
                    }
                    $userID = $user['user']->id;

                    $this->attachStatuses($dossier->id, $userID);

                    $this->makeOwners($dossier->id, $personId, true);
                } else {
                    $this->makeOwners($dossier->id, $personId, false);
                }
            }

            //store lawyers
            $lawyers = json_decode($data['lawyers']);
            foreach ($lawyers as $lawyer) {
                $createdOrUpdatedLawyer = $this->personUpdateOrInsert($lawyer);
                $personId = $createdOrUpdatedLawyer->id;
                $this->insertLicenses($personId, $lawyer);
                $this->insertLawyers($personId, $dossier->id);
            }

            //store estate data
            $this->makeEstate($data, $dossier->id);


            DB::commit();
            return response()->json(['data' => [
                'national_code' => $password,
                'mobile' => $mobile,
                'tracking_code' => $dossier->tracking_code,
            ]]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }


    }

    public function licenseList(Request $request)
    {
        $data = $request->all();
        $pageNum = $data['pageNum'] ?? 1;
        $perPage = $data['perPage'] ?? 10;

        $user = Auth::user();

        $user->load('employee');
        $employeeID = $user->employee->id;


        $scriptType = ScriptType::where('title', ScriptTypesEnum::MASOULE_FAANI->value)->first();


        $recruitmentScripts = RecruitmentScript::where('employee_id', $employeeID)->where('script_type_id', $scriptType->id)
            ->whereHas('latestStatus', function ($query) {
                $query->where('name', RecruitmentScriptStatusEnum::ACTIVE->value);
            })->get();

        if ($recruitmentScripts->count() == 0) {
            return response()->json(['message' => 'شما مسئول فنی نیستید'], 404);
        }

        $ounits = $recruitmentScripts->pluck('organization_unit_id')->unique()->toArray();

        $dossiers = $this->dossiersList($ounits, $perPage, $pageNum, $data);

        return LicensesListResource::collection($dossiers);


    }

    public function onlyLicenseTypesList()
    {


        $user = Auth::user();
        $user->load('employee');
        $employeeID = $user->employee->id;

        $scriptType = ScriptType::where('title', ScriptTypesEnum::MASOULE_FAANI->value)->first();

        $recruitmentScripts = RecruitmentScript::where('employee_id', $employeeID)->where('script_type_id', $scriptType->id)
            ->whereHas('latestStatus', function ($query) {
                $query->where('name', RecruitmentScriptStatusEnum::ACTIVE->value);
            })->get();

        $ounits = $recruitmentScripts->pluck('organization_unit_id')->unique()->toArray();

        $query = OrganizationUnit::query()
            ->join('organization_units as town', 'organization_units.parent_id', '=', 'town.id')
            ->join('organization_units as district', 'town.parent_id', '=', 'district.id')
            ->select([
                'district.id as id',
                'district.name as name',
            ])
            ->withoutGlobalScopes()
            ->whereIn('organization_units.id', $ounits)
            ->get();

        $bdmTypes = BdmTypesEnum::listWithIds();
        $permitStatuses = Status::where('model', PermitStatus::class)->orderBy('id')->select(['id', 'name'])->get();

        $bdmStatuses = Status::where('model', BuildingDossier::class)->select(['id', 'name'])->get();

        return response()->json(['bdm_types' => array_values($bdmTypes), 'permit_statuses' => $permitStatuses, 'districts' => $query, 'bdm_statuses' => $bdmStatuses], 200);
    }

    public function relatedDistrictList()
    {
        $user = Auth::user();
        $user->load('employee');
        $employeeID = $user->employee->id;

        $scriptType = ScriptType::where('title', ScriptTypesEnum::MASOULE_FAANI->value)->first();

        $recruitmentScripts = RecruitmentScript::where('employee_id', $employeeID)->where('script_type_id', $scriptType->id)
            ->whereHas('latestStatus', function ($query) {
                $query->where('name', RecruitmentScriptStatusEnum::ACTIVE->value);
            })->get();

        $ounits = $recruitmentScripts->pluck('organization_unit_id')->unique()->toArray();

        $query = OrganizationUnit::query()
            ->join('organization_units as town', 'organization_units.parent_id', '=', 'town.id')
            ->join('organization_units as district', 'town.parent_id', '=', 'district.id')
            ->select([
                'district.id as id',
                'district.name as name',
            ])
            ->withoutGlobalScopes()
            ->whereIn('organization_units.id', $ounits)
            ->get();

        return response()->json($query);

    }

    public function relatedVillagesList(Request $request)
    {
        $data = $request->all();
        $user = Auth::user();
        $user->load('employee');
        $employeeID = $user->employee->id;

        $scriptType = ScriptType::where('title', ScriptTypesEnum::MASOULE_FAANI->value)->first();

        $recruitmentScripts = RecruitmentScript::where('employee_id', $employeeID)->where('script_type_id', $scriptType->id)
            ->whereHas('latestStatus', function ($query) {
                $query->where('name', RecruitmentScriptStatusEnum::ACTIVE->value);
            })->get();


        $ounits = $recruitmentScripts->pluck('organization_unit_id')->unique()->toArray();


        $query = OrganizationUnit::query()
            ->join('organization_units as town', 'organization_units.parent_id', '=', 'town.id')
            ->join('districts as district', 'town.parent_id', '=', 'district.id')
            ->select([
                'organization_units.id as id',
                'organization_units.name as name',
            ])
            ->withoutGlobalScopes()
            ->whereIn('organization_units.id', $ounits)
            ->when(isset($data['districtID']), function ($query) use ($data) {
                $query->where('district.id', $data['districtID']);
            })
            ->get();

        return response()->json($query);
    }

    public function showDossier($id)
    {
        $getTimeLineData = $this->getTimelineData($id);
        $getFooterDatas = $this->getFooterDatas($id);
        $permitStatusesList = $this->getPermitStatusesList();

        return response()->json(['getTimeLineData' => $getTimeLineData, 'getFooterDatas' => $getFooterDatas, 'permitStatusesList' => $permitStatusesList]);

    }

    public function submitLicense($id)
    {
        try {
            DB::beginTransaction();
            $status = $this->upgradeOneLevel($id);
            DB::commit();
            return response()->json(['status' => $status]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function uploadFiles(Request $request, $id)
    {
        $data = $request->all();
        $files = json_decode($data['files']);
        $user = Auth::user();
        foreach ($files as $file) {
            $fileID = $file->id;
            $fileName = $file->name;
            $this->uploadFilesByStatus($id, $fileID, $fileName, $user);
        }
    }

    public function declineDossier(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $this->makeDossierDeclined($data, $id);
            DB::commit();
            return response()->json(['message' => "رد پرونده با موفقیت انجام پذیرفت"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getPersonData($id)
    {
        $person = Person::find($id);
        $person->load('personable');
        $person->personable->bc_issue_date = convertDateTimeGregorianToJalaliDateTime($person->personable->bc_issue_date);
        $person->personable->bc_issue_date = explode(' ', $person->personable->bc_issue_date)[0];

        $person->personable->date_of_birth = convertDateTimeGregorianToJalaliDateTime($person->personable->birth_date);
        $person->personable->date_of_birth = explode(' ', $person->personable->date_of_birth)[0];

        $person->military_service_status = MilitaryService::with('militaryServiceStatus')->where('person_id', $id)->first();
        return response()->json($person);
    }

    public function updatePerson($id, Request $request)
    {
        $data = $request->all();
        $person = Person::find($id);

        $person->display_name = $data['firstName'] . ' ' . $data['lastName'];

        $person->load('personable');
        $natural = $person->personable;
        $natural->first_name = $data['firstName'];
        $natural->last_name = $data['lastName'];
        $natural->gender_id = $data['gender'];
        $natural->father_name = $data['fatherName'];
        $natural->birth_date = convertJalaliPersianCharactersToGregorian($data['dateOfBirth']);
        $natural->bc_code = $data['bcCode'];
        $natural->birth_location = $data['birthLocation'];
        $natural->bc_serial = $data['bcSerial'];
        $natural->bc_issue_date = convertJalaliPersianCharactersToGregorian($data['issueDate']);
        $natural->bc_issue_location = $data['issueLocation'];

        $natural->save();
        $person->save();

        $militaryService = MilitaryService::where('person_id', $id)->first();
        if ($militaryService) {
            $militaryService->military_service_status_id = $data['militaryServiceStatusID'];
        }
    }

    public function sendDossierBill($id)
    {
        $buildings = $this->getBuildingBills($id);
        $partitioning = $this->getPartitioningBills($id);
        $pavilion = $this->getPavilionBills($id);
        $parking = $this->getParkingBills($id);
        $pool = $this->getPoolBills($id);
        $banks = $this->getBankAccs($id);
        $allTotalPrice = $buildings['total_price'] + $partitioning['total_price'] + $pavilion['total_price'] + $parking['total_price'] + $pool['total_price'];
        return response()->json(["buildings" => $buildings, "partitioning" => $partitioning, "pavilion" => $pavilion, "parking" => $parking, "pool" => $pool, "allTotalPrice" => $allTotalPrice, 'banks' => $banks]);
    }

    public function publishDossierBill(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $this->publishingDossierBill($data, $id);
            DB::commit();
            return response()->json(['message' => 'قبض با موفقیت صادر شد']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }


    public function generateFormA(Request $request,$id)
    {
        $data = [
            'component_to_render' => 'dossier_form_1_2',
            'model_id' => $id,
            'title' => DocumentsNameEnum::FORM_ALEF_ONE_TWO->value,
            'created_date' => Carbon::now(),
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
        $this->storeOdocDocument($data,$user->id);
    }
}
