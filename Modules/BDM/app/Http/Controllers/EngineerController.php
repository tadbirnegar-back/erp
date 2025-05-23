<?php

namespace Modules\BDM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\AAA\app\Http\Traits\UserTrait;
use Modules\BDM\app\Http\Enums\EngineersTypeEnum;
use Modules\BDM\app\Http\Traits\EngineerTrait;
use Modules\BDM\app\Models\Engineer;
use Modules\BDM\app\Models\EngineerBuilding;
use Modules\HRMS\app\Http\Traits\EducationRecordTrait;
use Modules\HRMS\app\Models\LevelOfEducation;
use Modules\HRMS\app\Models\MilitaryServiceStatus;
use Modules\PersonMS\app\Http\Traits\PersonTrait;
use Modules\PersonMS\app\Models\Natural;
use Modules\PersonMS\app\Models\Person;

class EngineerController extends Controller
{
    use PersonTrait, UserTrait, EducationRecordTrait, EngineerTrait;

    public function engineersRequestPreData()
    {
        $militaryServices = MilitaryServiceStatus::get();
        $levelsOfEducations = LevelOfEducation::get();

        return response()->json(['militaryServices' => $militaryServices, 'levelsOfEducations' => $levelsOfEducations]);

    }

    public function requestEngineer(Request $request)
    {
        try {
            $data = $request->all();
            $createdOrUpdatedPerson = $this->personUpdateOrInsert((object)$data);
            if (isset($createdOrUpdatedPerson['type'])) {
                return response()->json(['message' => 'شماره موبایل قبلا در سامانه ثبت شده'], 404);
            }
            $personId = $createdOrUpdatedPerson->id;
            $data['personID'] = $personId;
            $data['password'] = $data['nationalCode'];
            $user = $this->storeUserOrUpdate($data);
            $userID = $user['user']->id;
            $this->attachRoleForEngineer($userID);
            $this->EducationalRecordStore($data, $createdOrUpdatedPerson->id);
            $this->storeEngineer($data, $personId);
            $this->insertLicenses($personId, (object)$data);
            return response()->json(['data' => [
                'national_code' => $data['password'],
                'mobile' => $data['mobile'],
            ]]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

    }

    public function detectEngineer(Request $request)
    {
        $data = $request->all();

        $nationalCode = $data['nationalCode'];

        $person = Person::join('bdm_engineers', 'bdm_engineers.person_id', '=', 'persons.id')
            ->join('naturals', function ($join) {
                $join->on('persons.personable_id', '=', 'naturals.id')
                    ->where('persons.personable_type', '=', Natural::class);
            })
            ->where('national_code', $nationalCode)
            ->select([
                'bdm_engineers.id as id',
                'naturals.first_name as firstName',
                'naturals.last_name as lastName',
                'naturals.mobile as mobile',
            ])->first();

        if($person){
            return response()->json($person);
        }else{
            return response()->json(['message' => 'این شخص به عنوان مهندس در سامانه ثبت نشده است'], 404);
        }

    }

    public function addEngineers(Request $request, $id)
    {
        try {
            \DB::beginTransaction();
            $data = $request->all();
            $engineers = json_decode($data['engineer_ids']);
            foreach ($engineers as $engineer) {
                EngineerBuilding::updateOrCreate(
                    [
                        'dossier_id' => $id,
                        'engineer_type_id' => $engineer->engineer_type_id,
                    ],
                    [
                        'engineer_id' => $engineer->id,
                    ]
                );

            }
            \DB::commit();
            return response()->json(['message' => "افزودن مهندسان با موفیت انجام شد"]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }

    }

    public function engineersTypeList()
    {
        $list = EngineersTypeEnum::listWithIds();
        return response()->json($list);
    }
}
