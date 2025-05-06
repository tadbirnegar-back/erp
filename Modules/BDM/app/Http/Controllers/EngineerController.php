<?php

namespace Modules\BDM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\AAA\app\Http\Traits\UserTrait;
use Modules\BDM\app\Http\Traits\EngineerTrait;
use Modules\HRMS\app\Http\Traits\EducationRecordTrait;
use Modules\HRMS\app\Models\LevelOfEducation;
use Modules\HRMS\app\Models\MilitaryServiceStatus;
use Modules\PersonMS\app\Http\Traits\PersonTrait;

class EngineerController extends Controller
{
    use PersonTrait, UserTrait , EducationRecordTrait , EngineerTrait;

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

            $password = '';
            $mobile = '';
            //store owners and partners
            $createdOrUpdatedPerson = $this->personUpdateOrInsert((object)$data);
            if (isset($createdOrUpdatedPerson['type'])) {
                return response()->json(['message' => 'شماره موبایل قبلا در سامانه ثبت شده'], 404);
            }
            $personId = $createdOrUpdatedPerson->id;
            $data['personID'] = $personId;
            $data['password'] = $data['nationalCode'];
            $user = $this->storeUserOrUpdate($data);
            $this->EducationalRecordStore($data,$createdOrUpdatedPerson->id);
            $this->storeEngineer($data , $personId);
            return response()->json(['data' => [
                'national_code' => $data['password'],
                'mobile' => $data['mobile'],
            ]]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

    }
}
