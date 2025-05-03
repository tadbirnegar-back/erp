<?php

namespace Modules\BDM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\AAA\app\Http\Traits\UserTrait;
use Modules\AAA\app\Models\User;
use Modules\BDM\app\Http\Enums\BdmOwnershipTypesEnum;
use Modules\BDM\app\Http\Enums\BdmTypesEnum;
use Modules\BDM\app\Http\Enums\TransferTypesEnum;
use Modules\BDM\app\Http\Traits\DossierTrait;
use Modules\BDM\app\Http\Traits\EstateTrait;
use Modules\BDM\app\Http\Traits\LawyersTrait;
use Modules\BDM\app\Http\Traits\OwnersTrait;
use Modules\HRMS\app\Http\Enums\RecruitmentScriptStatusEnum;
use Modules\HRMS\app\Http\Enums\ScriptTypesEnum;
use Modules\HRMS\app\Models\ExemptionType;
use Modules\HRMS\app\Models\MilitaryService;
use Modules\HRMS\app\Models\MilitaryServiceStatus;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Models\ScriptType;
use Modules\OUnitMS\app\Models\CityOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\VillageOfc;
use Modules\PersonMS\app\Http\Traits\PersonTrait;
use Modules\PersonMS\app\Models\Natural;
use Modules\PersonMS\app\Models\Person;
use Modules\PFM\app\Models\Application;

class LicenseController extends Controller
{
    use PersonTrait, UserTrait, DossierTrait, OwnersTrait, LawyersTrait, EstateTrait;

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

    public function create(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $dossier = $this->makeDossier($data['ounitID'], $data['ownershipTypeID']);

            $password = '';
            $mobile = '';
            //store owners and partners
            $persons = json_decode($data['persons']);

            foreach ($persons as $key => $person) {

                $createdOrUpdatedPerson = $this->personUpdateOrInsert($person);
                if(isset($createdOrUpdatedPerson['type']))
                {
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

        $user = User::find(2174);

        $user->load('employee');
        $employeeID = $user->employee->id;


        $scriptType = ScriptType::where('title' , ScriptTypesEnum::MASOULE_FAANI->value)->first();


        $recruitmentScripts = RecruitmentScript::where('employee_id', $employeeID)->where('script_type_id' , $scriptType->id)
            ->whereHas('latestStatus' , function ($query) {
                $query->where('name' , RecruitmentScriptStatusEnum::ACTIVE->value);
            })->get();

        if($recruitmentScripts->count() == 0){
            return response()->json(['message' => 'شما مسئول فنی نیستید'] , 404);
        }

        $ounits = $recruitmentScripts->pluck('organization_unit_id')->unique()->toArray();

        $dossiers = $this->dossiersList($ounits , $perPage , $pageNum);

        return response()->json($dossiers);


    }
}
