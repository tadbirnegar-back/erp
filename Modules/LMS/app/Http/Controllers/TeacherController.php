<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\HRMS\app\Http\Traits\EducationRecordTrait;
use Modules\LMS\app\Http\Traits\TeacherTrait;

class TeacherController extends Controller
{
    use TeacherTrait, EducationRecordTrait;

    public function store(Request $request): JsonResponse
    {
        try {
            \DB::beginTransaction();
            $data = $request->all();
            $situation = $this->isPersonTeacher($data['nationalCode']);
            $data['bcIssueDate'] = convertJalaliPersianCharactersToGregorian($data['bcIssueDate']);
            $data['dateOfBirth'] = convertJalaliPersianCharactersToGregorian($data['dateOfBirth']);
            if ($situation['message'] == "teacher") {
                return response()->json(["message" => "شخصی با این کد ملی قبلا به عنوان مدرس افزوده شده است"], 409);
            } elseif ($situation['message'] == "found") {
                $person = $this->personNaturalUpdate($data, $situation['data']['result']);
                $workforce = $this->storeTeacher($data, $person->id)->workForce;
            } else {
                $natural = $this->naturalStore($data);
                $person = $natural->person;
                $workforce = $this->storeTeacher($data, $person->id)->workForce;
            }

            if (isset($data['educations'])) {
                $edus = json_decode($data['educations'], true);
                $this->educationUpsert($edus, $workforce->id);
            }

            if(isset($data['deletedEducations'])){
                $this->EducationHardDelete($data['deletedEducations']);
            }

            \DB::commit();
            return response()->json(["message" => "مدرس با موفقیت افزوده شد"], 200);

        } catch (\Exception $exception) {
            \DB::rollBack();
            return response()->json(['message' => $exception->getMessage() , 'trance' => $exception -> getTrace()], 500);
        }

    }

    public function isTeacherExist(Request $request): JsonResponse
    {
        $situation = $this->isPersonTeacher($request->nationalCode);
        return response()->json($situation);
    }
}
