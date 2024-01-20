<?php

namespace Modules\PersonMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\PersonMS\app\Models\Natural;
use Modules\PersonMS\app\Models\Person;

class PersonMSController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    public function naturalStore(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $naturalPerson = new Natural();
            $naturalPerson->first_name = $request->firstName;
            $naturalPerson->last_name = $request->lastName;
            $naturalPerson->father_name = $request->fatherName;
            $naturalPerson->birth_date = $request->birthDate;
            $naturalPerson->gender_id = $request->gender;
            $naturalPerson->isMarried = $request->marriage ?? false;
            $naturalPerson->level_of_spouse_education = $request->levelOfSpouseEducation ?? null;
            $naturalPerson->spouse_first_name = $request->spouseFirstName ?? null;
            $naturalPerson->spouse_last_name = $request->spouseLastName ?? null;
            $naturalPerson->military_service_status_id = $request->military_service_status_id ?? null;
            $naturalPerson->mobile = $request->mobile;
//            $naturalPerson->person()->create([
//                'display_name' => $naturalPerson->first_name . ' ' . $naturalPerson->last_name,
//                'national_code' => $request->national_code,
//                'profile_picture_id' => $request->profile_picture_id,
//                ]);

//            $naturalPerson->save();
            $person = new Person();
            $person->display_name = $naturalPerson->first_name . ' ' . $naturalPerson->last_name;
            $person->national_code = $request->national_code;
            $person->profile_picture_id = $request->profile_picture_id;

            $naturalPerson->person()->save($person);



            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
        }

    }
}
